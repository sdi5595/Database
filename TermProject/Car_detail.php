<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if (isset($_GET['Model']) && isset($_GET['DriverID'])) {
    $Model = urldecode($_GET['Model']);
    $DriverID = $_GET['DriverID'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    $query = "SELECT * FROM Car WHERE Model = '$Model' AND DriverID = '$DriverID'";
    $result = mysqli_query($conn, $query);
    $car = mysqli_fetch_array($result);
    if (!$car) {
        msg('해당 차량 정보를 찾을 수 없습니다.');
    }
} else {
    msg('잘못된 접근입니다.');
}
?>

<div class="container">
    <h2>차량 상세 정보</h2>
    <?php if ($car) { ?>
    <table class="table table-striped table-bordered">
        <tr>
            <th>차량 모델명</th>
            <td><?= $car['Model'] ?></td>
        </tr>
        <tr>
            <th>연비</th>
            <td><?= $car['Mileage'] ?></td>
        </tr>
        <tr>
            <th>배송기사ID</th>
            <td><?= $car['DriverID'] ?></td>
        </tr>
    </table>
    <?php } ?>
    <div style="text-align: right; margin-top: 10px;">
        <a href="Driver_list.php" class="btn btn-primary">목록으로 돌아가기</a>
    </div>
</div>

<?php
mysqli_close($conn);
include "footer.php";
?>
