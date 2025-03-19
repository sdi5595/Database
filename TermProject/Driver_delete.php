<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DriverID = $_POST['DriverID'];
    $resignedDriverID = '퇴사전용';  // 퇴사한 배송기사ID를 '퇴사전용'으로 설정

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // DeliveryDetail 테이블에서 해당 DriverID를 '퇴사전용'으로 설정
        $query = "UPDATE DeliveryDetail SET DriverID = '$resignedDriverID' WHERE DriverID = '$DriverID'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('DeliveryDetail에서 DriverID를 퇴사전용으로 설정하는데 실패했습니다: ' . mysqli_error($conn));
        }

        // DriverPhone 테이블에서 해당 DriverID와 관련된 데이터 삭제
        $query = "DELETE FROM DriverPhone WHERE DriverID = '$DriverID'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('DriverPhone에서 삭제에 실패했습니다: ' . mysqli_error($conn));
        }

        // DeliveryDriver 테이블에서 데이터 삭제
        $query = "DELETE FROM DeliveryDriver WHERE DriverID = '$DriverID'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('DeliveryDriver에서 삭제에 실패했습니다: ' . mysqli_error($conn));
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 삭제되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Driver_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Driver_list.php'>";
    }

    mysqli_close($conn);
}
?>
