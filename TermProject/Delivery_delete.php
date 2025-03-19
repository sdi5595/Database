<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DeliveryNumber = $_POST['DeliveryNumber'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 공급번호 삭제
        $query = "UPDATE BabyFood SET DeliveryNumber = NULL WHERE DeliveryNumber = '$DeliveryNumber'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('공급번호 삭제에 실패했습니다.');
        }

        // 주문 정보 삭제
        $query = "DELETE FROM `Order` WHERE DeliveryNumber = '$DeliveryNumber'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('주문 정보 삭제에 실패했습니다.');
        }

        // 배송 정보 삭제
        $query = "DELETE FROM DeliveryDetail WHERE DeliveryNumber = '$DeliveryNumber'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('배송 정보 삭제에 실패했습니다.');
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 삭제되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Delivery_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Delivery_list.php'>";
    }

    mysqli_close($conn);
}
?>
