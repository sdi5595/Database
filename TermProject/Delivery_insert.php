<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DeliveryNumber = $_POST['DeliveryNumber'];
    $OrderDate = $_POST['OrderDate'];
    $DeliveryDate = $_POST['DeliveryDate'];
    $DeliveryStatus = $_POST['DeliveryStatus'];
    $DriverID = $_POST['DriverID'];
    $MemberID = $_POST['MemberID'];
    $SupplyNumber = $_POST['SupplyNumber'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 배송기사ID와 회원ID가 유효한지 확인 (배송기사ID는 선택 사항이므로 존재하는 경우에만 확인)
        if (!empty($DriverID)) {
            $query = "SELECT * FROM DeliveryDriver WHERE DriverID = '$DriverID'";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) == 0) {
                throw new Exception('유효한 배송기사ID가 아닙니다.');
            }
        }

        $query = "SELECT * FROM Member WHERE MemberID = '$MemberID'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            throw new Exception('유효한 회원ID가 아닙니다.');
        }

        // 공급번호가 이미 사용 중인지 확인
        $supply_numbers = explode(',', $SupplyNumber);
        foreach ($supply_numbers as $supply_number) {
            $supply_number = trim($supply_number);
            $check_query = "SELECT * FROM BabyFood WHERE SupplyNumber = '$supply_number' AND DeliveryNumber IS NOT NULL";
            $check_result = mysqli_query($conn, $check_query);
            if (mysqli_num_rows($check_result) > 0) {
                throw new Exception('공급번호가 이미 사용 중입니다.');
            }
        }

        // 배송 정보 등록
        $query = "INSERT INTO DeliveryDetail (DeliveryNumber, OrderDate, DeliveryDate, DeliveryStatus, DriverID) 
                  VALUES ('$DeliveryNumber', '$OrderDate', '$DeliveryDate', '$DeliveryStatus', " . (empty($DriverID) ? "NULL" : "'$DriverID'") . ")";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('배송 정보 등록에 실패했습니다.');
        }

        // 주문 정보 등록
        $query = "INSERT INTO `Order` (DeliveryNumber, MemberID) VALUES ('$DeliveryNumber', '$MemberID')";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('주문 정보 등록에 실패했습니다.');
        }

        // 공급번호 등록
        foreach ($supply_numbers as $supply_number) {
            $supply_number = trim($supply_number);
            $query = "UPDATE BabyFood SET DeliveryNumber = '$DeliveryNumber' WHERE SupplyNumber = '$supply_number'";
            if (!mysqli_query($conn, $query)) {
                throw new Exception('공급번호 등록에 실패했습니다.');
            }
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 추가되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Delivery_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Delivery_form.php'>";
    }

    mysqli_close($conn);
}
?>
