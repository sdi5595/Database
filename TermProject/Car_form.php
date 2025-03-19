<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$Model = "";
$Mileage = "";
$DriverID = "";
$mode = "등록";

if (isset($_GET['Model']) && isset($_GET['DriverID'])) {
    $mode = "수정";
    $conn = dbconnect($host, $dbid, $dbpass, $dbname);
    $Model = $_GET['Model'];
    $DriverID = $_GET['DriverID'];
    
    $query = "SELECT * FROM Car WHERE Model = '$Model' AND DriverID = '$DriverID'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $Mileage = $row['Mileage'];

    mysqli_close($conn);
}
?>

<div class="container">
    <h2>차량 <?= $mode ?></h2>
    <form action="Car_<?= $mode == '등록' ? 'insert' : 'modify' ?>.php" method="post">
        <div class="form-group">
            <label for="Model">차량 모델명:</label>
            <input type="text" class="form-control" id="Model" name="Model" value="<?= $Model ?>" <?= $mode == '수정' ? 'readonly' : '' ?> required>
        </div>
        <div class="form-group">
            <label for="Mileage">연비:</label>
            <input type="text" class="form-control" id="Mileage" name="Mileage" value="<?= $Mileage ?>" required>
        </div>
        <div class="form-group">
            <label for="DriverID">배송기사ID:</label>
            <?php if ($mode == '등록') { ?>
            <input type="text" class="form-control" id="DriverID" name="DriverID" value="<?= $DriverID ?>" required>
            <?php } else { ?>
            <input type="text" class="form-control" id="DriverID" value="<?= $DriverID ?>" disabled>
            <input type="hidden" name="DriverID" value="<?= $DriverID ?>">
            <?php } ?>
        </div>
        <button type="submit" class="btn btn-primary"><?= $mode ?></button>
        <a href="Car_list.php" class="btn btn-secondary">취소</a>
    </form>
</div>

<?php include "footer.php"; ?>
