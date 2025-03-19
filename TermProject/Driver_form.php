<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$DriverID = "";
$DriverName = "";
$Area = "";
$PhoneNumber = "";
$CarModels = "";
$CarMileages = "";
$mode = "등록";

if (isset($_GET['DriverID'])) {
    $mode = "수정";
    $conn = dbconnect($host, $dbid, $dbpass, $dbname);
    $DriverID = $_GET['DriverID'];
    
    $query = "SELECT d.DriverID, d.DriverName, d.Area, 
                     GROUP_CONCAT(c.Model SEPARATOR ', ') as CarModels,
                     GROUP_CONCAT(c.Mileage SEPARATOR ', ') as CarMileages 
              FROM DeliveryDriver d 
              LEFT JOIN Car c ON d.DriverID = c.DriverID
              WHERE d.DriverID = '$DriverID'
              GROUP BY d.DriverID, d.DriverName, d.Area";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $DriverName = $row['DriverName'];
    $Area = $row['Area'];
    $CarModels = $row['CarModels'];
    $CarMileages = $row['CarMileages'];

    mysqli_close($conn);
}
?>

<div class="container">
    <h2>기사 <?= $mode ?></h2>
    <form action="Driver_<?= $mode == '등록' ? 'insert' : 'modify' ?>.php" method="post">
        <div class="form-group">
            <label for="DriverID">배송기사 ID:</label>
            <input type="text" class="form-control" id="DriverID" name="DriverID" value="<?= $DriverID ?>" maxlength="6" <?= $mode == '수정' ? 'readonly' : '' ?> required>
        </div>
        <div class="form-group">
            <label for="DriverName">배송기사명:</label>
            <input type="text" class="form-control" id="DriverName" name="DriverName" value="<?= $DriverName ?>" required>
        </div>
        <div class="form-group">
            <label for="Area">담당지역:</label>
            <input type="text" class="form-control" id="Area" name="Area" value="<?= $Area ?>" required>
        </div>
        <?php if ($mode == '등록') { ?>
        <div class="form-group">
            <label for="PhoneNumber">휴대폰 번호:</label>
            <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="<?= $PhoneNumber ?>">
            <small class="form-text text-muted">여러 개의 전화번호는 쉼표로 구분하여 입력하세요.</small>
        </div>
        <?php } ?>
        <div class="form-group">
            <label for="CarModels">차량 모델명:</label>
            <input type="text" class="form-control" id="CarModels" name="CarModels" value="<?= $CarModels ?>" required>
            <small class="form-text text-muted">여러 개의 차량 모델명은 쉼표로 구분하여 입력하세요.</small>
        </div>
        <div class="form-group">
            <label for="CarMileages">차량 연비:</label>
            <input type="text" class="form-control" id="CarMileages" name="CarMileages" value="<?= $CarMileages ?>" required>
            <small class="form-text text-muted">여러 개의 차량 연비는 쉼표로 구분하여 입력하세요. 차량 모델명과 순서가 일치해야 합니다.</small>
        </div>
        <button type="submit" class="btn btn-primary"><?= $mode ?></button>
        <?php if ($mode == '등록') { ?>
            <a href="Driver_list.php" class="btn btn-secondary">취소</a>
        <?php } ?>
    </form>
</div>
