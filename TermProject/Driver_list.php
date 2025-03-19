<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$search = isset($_POST['search']) ? $_POST['search'] : '';

$conn = dbconnect($host, $dbid, $dbpass, $dbname);

$query = "SELECT DriverID, DriverName, Area, COALESCE(GROUP_CONCAT(PhoneNumber SEPARATOR ', '), '-') as PhoneNumbers, 
          (SELECT GROUP_CONCAT(Model SEPARATOR ', ') FROM Car WHERE Car.DriverID = DeliveryDriver.DriverID) as CarModels
          FROM DeliveryDriver 
          LEFT JOIN DriverPhone USING (DriverID)
          WHERE DriverID != '퇴사전용'";

if ($search) {
    $query .= " AND (DriverID LIKE '%$search%' OR DriverName LIKE '%$search%')";
}

$query .= " GROUP BY DriverID, DriverName, Area";

$result = mysqli_query($conn, $query);
?>

<div class="container">
    <h2>기사 목록</h2>
    <form method="post" action="Driver_list.php">
        <div class="form-group">
            <input type="text" class="form-control" name="search" placeholder="검색어를 입력해주세요" value="<?= $search ?>">
        </div>
        <button type="submit" class="btn btn-primary">검색</button>
        <small class="form-text text-muted">배송기사ID나 배송기사명을 입력해주세요.</small>
    </form>
    <br>
    <table class="table table-striped table-bordered">
        <tr>
            <th>배송기사ID</th>
            <th>배송기사명</th>
            <th>담당지역</th>
            <th>휴대폰번호</th>
            <th>차량 모델명</th>
            <th>수정/삭제</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>{$row['DriverID']}</td>";
            echo "<td>{$row['DriverName']}</td>";
            echo "<td>{$row['Area']}</td>";
            echo "<td>{$row['PhoneNumbers']}</td>";
            echo "<td>";
            if ($row['CarModels']) {
                $car_models = explode(', ', $row['CarModels']);
                foreach ($car_models as $car_model) {
                    $encoded_model = urlencode($car_model);
                    echo "<a href='Car_detail.php?Model={$encoded_model}&DriverID={$row['DriverID']}'>{$car_model}</a> ";
                }
            }
            echo "</td>";
            echo "<td>
                    <form action='Driver_form.php' method='get' style='display:inline;'>
                        <input type='hidden' name='DriverID' value='{$row['DriverID']}'>
                        <button type='submit' class='btn btn-warning'>수정</button>
                    </form>
                    <form action='Driver_delete.php' method='post' style='display:inline;' onsubmit='return confirm(\"정말 삭제하시겠습니까?\");'>
                        <input type='hidden' name='DriverID' value='{$row['DriverID']}'>
                        <button type='submit' class='btn btn-danger'>삭제</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
    <div style="text-align: right; margin-top: 10px;">
        <a href="Driver_form.php" class="btn btn-primary">기사 등록</a>
    </div>
</div>

<?php
mysqli_close($conn);
include "footer.php";
?>
