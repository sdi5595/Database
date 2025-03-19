<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if (isset($_GET['DeliveryNumber'])) {
    $DeliveryNumber = $_GET['DeliveryNumber'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    $query = "SELECT d.DeliveryNumber, d.OrderDate, d.DeliveryDate, d.DeliveryStatus, d.DriverID, 
                     o.MemberID, m.MemberName AS MemberName, m.AddressCity AS Address, m.AddressDetail, mp.PhoneNumber, 
                     GROUP_CONCAT(b.SupplyNumber SEPARATOR ', ') AS SupplyNumbers
              FROM DeliveryDetail d
              JOIN `Order` o ON d.DeliveryNumber = o.DeliveryNumber
              JOIN Member m ON o.MemberID = m.MemberID
              LEFT JOIN MemberPhone mp ON m.MemberID = mp.MemberID
              LEFT JOIN BabyFood b ON d.DeliveryNumber = b.DeliveryNumber
              WHERE d.DeliveryNumber = '$DeliveryNumber'
              GROUP BY d.DeliveryNumber";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        mysqli_query($conn, "rollback");
        echo "쿼리 오류: " . mysqli_error($conn);
        exit;
    } else {
        $row = mysqli_fetch_array($result);
        // 트랜잭션 커밋
        mysqli_query($conn, "commit");
    }

    mysqli_close($conn);
} else {
    echo "잘못된 접근입니다.";
    exit;
}
?>

<div class="container">
    <h2>배송 상세 정보</h2>
    <table class="table table-striped table-bordered">
        <tr>
            <th>배송번호</th>
            <td><?= $row['DeliveryNumber'] ?></td>
        </tr>
        <tr>
            <th>주문일자</th>
            <td><?= $row['OrderDate'] ?></td>
        </tr>
        <tr>
            <th>배송일자</th>
            <td><?= $row['DeliveryStatus'] == '배송전' ? '-' : $row['DeliveryDate'] ?></td>
        </tr>
        <tr>
            <th>배송상태</th>
            <td><?= $row['DeliveryStatus'] ?></td>
        </tr>
        <tr>
            <th>담당배송기사ID</th>
            <td><?= $row['DriverID'] ?></td>
        </tr>
        <tr>
            <th>회원ID</th>
            <td><?= $row['MemberID'] ?></td>
        </tr>
        <tr>
            <th>회원이름</th>
            <td><?= $row['MemberName'] ?></td>
        </tr>
        <tr>
            <th>주소</th>
            <td><?= $row['Address'] ?></td>
        </tr>
        <tr>
            <th>상세주소</th>
            <td><?= $row['AddressDetail'] ?></td>
        </tr>
        <tr>
            <th>회원전화번호</th>
            <td><?= $row['PhoneNumber'] ?></td>
        </tr>
        <tr>
            <th>공급번호</th>
            <td><?= $row['SupplyNumbers'] ?></td>
        </tr>
    </table>
    <div style="text-align: right; margin-top: 10px;">
        <a href="Delivery_list.php" class="btn btn-secondary">목록으로 돌아가기</a>
    </div>
</div>

<?php include "footer.php"; ?>
