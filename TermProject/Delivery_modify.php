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

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 배송 정보 수정
        $query = "UPDATE DeliveryDetail SET OrderDate = '$OrderDate', DeliveryDate = '$DeliveryDate', 
                  DeliveryStatus = '$DeliveryStatus', DriverID = '$DriverID' 
                  WHERE DeliveryNumber = '$DeliveryNumber'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('배송 정보 수정에 실패했습니다.');
        }

        // 주문 정보 수정
        $query = "UPDATE `Order` SET MemberID = '$MemberID' WHERE DeliveryNumber = '$DeliveryNumber'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('회원 정보 수정에 실패했습니다.');
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 수정되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Delivery_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Delivery_form.php?DeliveryNumber={$DeliveryNumber}'>";
    }

    mysqli_close($conn);
}
?>
