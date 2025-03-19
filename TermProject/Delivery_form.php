<?php
include "header.php";
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

$DeliveryNumber = "";
$OrderDate = "";
$DeliveryDate = "";
$DeliveryStatus = "배송전";
$DriverID = "";
$MemberID = "";
$SupplyNumber = "";
$mode = "등록";

if (isset($_GET['DeliveryNumber'])) {
    $mode = "수정";
    $conn = dbconnect($host, $dbid, $dbpass, $dbname);
    $DeliveryNumber = $_GET['DeliveryNumber'];
    
    $query = "SELECT d.*, o.MemberID 
              FROM DeliveryDetail d
              JOIN `Order` o ON d.DeliveryNumber = o.DeliveryNumber
              WHERE d.DeliveryNumber = '$DeliveryNumber'";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $OrderDate = $row['OrderDate'];
    $DeliveryDate = $row['DeliveryDate'];
    $DeliveryStatus = $row['DeliveryStatus'];
    $DriverID = $row['DriverID'];
    $MemberID = $row['MemberID'];
    
    $query = "SELECT GROUP_CONCAT(SupplyNumber SEPARATOR ', ') AS SupplyNumbers
              FROM BabyFood
              WHERE DeliveryNumber = '$DeliveryNumber'";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $SupplyNumber = $row['SupplyNumbers'];

    mysqli_close($conn);
}
?>

<div class="container">
    <h2>배송 <?= $mode ?></h2>
    <form action="Delivery_<?= $mode == '등록' ? 'insert' : 'modify' ?>.php" method="post">
        <?php if ($mode == '등록') { ?>
        <div class="form-group">
            <label for="DeliveryNumber">배송번호:</label>
            <input type="text" class="form-control" id="DeliveryNumber" name="DeliveryNumber" value="<?= $DeliveryNumber ?>" required>
        </div>
        <?php } else { ?>
        <input type="hidden" name="DeliveryNumber" value="<?= $DeliveryNumber ?>">
        <?php } ?>
        <div class="form-group">
            <label for="OrderDate">주문일자:</label>
            <input type="date" class="form-control" id="OrderDate" name="OrderDate" value="<?= $OrderDate ?>" required>
        </div>
        <div class="form-group">
            <label for="DeliveryDate">배송완료일자:</label>
            <input type="date" class="form-control" id="DeliveryDate" name="DeliveryDate" value="<?= $DeliveryDate ?>">
        </div>
        <div class="form-group">
            <label for="DeliveryStatus">배송상태:</label>
            <select class="form-control" id="DeliveryStatus" name="DeliveryStatus" required>
                <option value="배송전" <?= $DeliveryStatus == '배송전' ? 'selected' : '' ?>>배송전</option>
                <option value="배송완료" <?= $DeliveryStatus == '배송완료' ? 'selected' : '' ?>>배송완료</option>
            </select>
        </div>
        <div class="form-group">
            <label for="DriverID">배송기사ID:</label>
            <input type="text" class="form-control" id="DriverID" name="DriverID" value="<?= $DriverID ?>">
            <small class="form-text text-muted">배송기사 ID는 선택 사항입니다.</small>
        </div>
        <div class="form-group">
            <label for="MemberID">회원ID:</label>
            <input type="text" class="form-control" id="MemberID" name="MemberID" value="<?= $MemberID ?>" required>
        </div>
        <div class="form-group">
            <label for="SupplyNumber">공급번호:</label>
            <input type="text" class="form-control" id="SupplyNumber" name="SupplyNumber" value="<?= $SupplyNumber ?>" <?= $mode == '수정' ? 'readonly' : '' ?> required>
            <small class="form-text text-muted"><?= $mode == '수정' ? '공급번호는 수정할 수 없습니다.' : '여러 개의 공급번호는 쉼표로 구분하여 입력하세요.' ?></small>
        </div>
        <button type="submit" class="btn btn-primary"><?= $mode ?></button>
        <a href="Delivery_list.php" class="btn btn-secondary">취소</a>
    </form>
</div>

<?php include "footer.php"; ?>
