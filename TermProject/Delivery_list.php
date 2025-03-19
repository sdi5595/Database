<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$search_driver_id = isset($_POST['search_driver_id']) ? $_POST['search_driver_id'] : '';
$search_status = isset($_POST['search_status']) ? $_POST['search_status'] : '';

$conn = dbconnect($host, $dbid, $dbpass, $dbname);

// 트랜잭션 시작
mysqli_query($conn, "set autocommit = 0");
mysqli_query($conn, "set session transaction isolation level serializable");
mysqli_query($conn, "begin");

$query = "SELECT d.DeliveryNumber, d.DeliveryStatus, d.DriverID, m.AddressCity AS Address, m.AddressDetail, 
                 GROUP_CONCAT(b.SupplyNumber SEPARATOR ', ') AS SupplyNumbers
          FROM DeliveryDetail d
          JOIN `Order` o ON d.DeliveryNumber = o.DeliveryNumber
          JOIN Member m ON o.MemberID = m.MemberID
          LEFT JOIN BabyFood b ON d.DeliveryNumber = b.DeliveryNumber
          WHERE 1=1";

if ($search_driver_id) {
    $query .= " AND d.DriverID = '$search_driver_id'";
}

if ($search_status) {
    $query .= " AND d.DeliveryStatus = '$search_status'";
}

$query .= " GROUP BY d.DeliveryNumber, d.DeliveryStatus, d.DriverID, m.AddressCity, m.AddressDetail";

$result = mysqli_query($conn, $query);
if (!$result) {
    mysqli_query($conn, "rollback");
    echo "쿼리 오류: " . mysqli_error($conn);
    exit;
} else {
    // 트랜잭션 커밋
    mysqli_query($conn, "commit");
}
?>

<div class="container">
    <h2>배송 조회</h2>
    <form method="post" action="Delivery_list.php">
        <div class="form-group">
            <label for="search_driver_id">배송기사ID:</label>
            <input type="text" class="form-control" id="search_driver_id" name="search_driver_id" value="<?= $search_driver_id ?>" placeholder="배송기사ID를 입력하세요">
        </div>
        <div class="form-group">
            <label for="search_status">배송상태:</label>
            <select class="form-control" id="search_status" name="search_status">
                <option value="">-- 선택하세요 --</option>
                <option value="배송전" <?= $search_status == '배송전' ? 'selected' : '' ?>>배송전</option>
                <option value="배송완료" <?= $search_status == '배송완료' ? 'selected' : '' ?>>배송완료</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">검색</button>
    </form>
    <br>
    <table class="table table-striped table-bordered">
        <tr>
            <th>배송번호</th>
            <th>배송상태</th>
            <th>배송기사ID</th>
            <th>주소</th>
            <th>상세주소</th>
            <th>공급번호</th>
            <th>수정/삭제</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td><a href='Delivery_detail.php?DeliveryNumber={$row['DeliveryNumber']}'>{$row['DeliveryNumber']}</a></td>";
            echo "<td>{$row['DeliveryStatus']}</td>";
            echo "<td>{$row['DriverID']}</td>";
            echo "<td>{$row['Address']}</td>";
            echo "<td>{$row['AddressDetail']}</td>";
            echo "<td>{$row['SupplyNumbers']}</td>";
            echo "<td>
                    <form action='Delivery_form.php' method='get' style='display:inline;'>
                        <input type='hidden' name='DeliveryNumber' value='{$row['DeliveryNumber']}'>
                        <button type='submit' class='btn btn-warning'>수정</button>
                    </form>
                    <form action='Delivery_delete.php' method='post' style='display:inline;' onsubmit='return confirm(\"정말 삭제하시겠습니까?\");'>
                        <input type='hidden' name='DeliveryNumber' value='{$row['DeliveryNumber']}'>
                        <button type='submit' class='btn btn-danger'>삭제</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
    <div style="text-align: right; margin-top: 10px;">
        <a href="Delivery_form.php" class="btn btn-primary">배송 등록</a>
    </div>
</div>

<?php
mysqli_close($conn);
include "footer.php";
?>
