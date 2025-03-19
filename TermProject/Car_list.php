<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$search_model = isset($_POST['search_model']) ? $_POST['search_model'] : '';
$search_driver_id = isset($_POST['search_driver_id']) ? $_POST['search_driver_id'] : '';

$conn = dbconnect($host, $dbid, $dbpass, $dbname);

// 트랜잭션 시작
mysqli_query($conn, "set autocommit = 0");
mysqli_query($conn, "set session transaction isolation level serializable");
mysqli_query($conn, "begin");

$query = "SELECT Model, Mileage, DriverID FROM Car WHERE DriverID != '퇴사전용'";

if ($search_model) {
    $query .= " AND Model LIKE '%$search_model%'";
}

if ($search_driver_id) {
    $query .= " AND DriverID = '$search_driver_id'";
}

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
    <h2>차량 목록</h2>
    <form method="post" action="Car_list.php">
        <div class="form-group">
            <label for="search_model">차량 모델명:</label>
            <input type="text" class="form-control" id="search_model" name="search_model" value="<?= $search_model ?>" placeholder="차량 모델명을 입력하세요">
        </div>
        <div class="form-group">
            <label for="search_driver_id">배송기사ID:</label>
            <input type="text" class="form-control" id="search_driver_id" name="search_driver_id" value="<?= $search_driver_id ?>" placeholder="배송기사ID를 입력하세요">
        </div>
        <button type="submit" class="btn btn-primary">검색</button>
    </form>
    <br>
    <table class="table table-striped table-bordered">
        <tr>
            <th>차량 모델명</th>
            <th>연비</th>
            <th>배송기사ID</th>
            <th>수정/삭제</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>{$row['Model']}</td>";
            echo "<td>{$row['Mileage']}</td>";
            echo "<td>{$row['DriverID']}</td>";
            echo "<td>
                    <form action='Car_form.php' method='get' style='display:inline;'>
                        <input type='hidden' name='Model' value='{$row['Model']}'>
                        <input type='hidden' name='DriverID' value='{$row['DriverID']}'>
                        <button type='submit' class='btn btn-warning'>수정</button>
                    </form>
                    <form action='Car_delete.php' method='post' style='display:inline;' onsubmit='return confirm(\"정말 삭제하시겠습니까?\");'>
                        <input type='hidden' name='Model' value='{$row['Model']}'>
                        <input type='hidden' name='DriverID' value='{$row['DriverID']}'>
                        <button type='submit' class='btn btn-danger'>삭제</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
    <div style="text-align: right; margin-top: 10px;">
        <a href="Car_form.php" class="btn btn-primary">차량 등록</a>
    </div>
</div>

<?php
mysqli_close($conn);
include "footer.php";
?>
