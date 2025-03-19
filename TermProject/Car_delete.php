<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Model = $_POST['Model'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 차량 정보 가져오기
        $query = "SELECT * FROM Car WHERE Model = '$Model'";
        $result = mysqli_query($conn, $query);
        $car = mysqli_fetch_array($result);
        if (!$car) {
            throw new Exception('차량 정보를 찾을 수 없습니다.');
        }

        $DriverID = $car['DriverID'];

        // 해당 배송기사의 차량 개수 확인
        $query = "SELECT COUNT(*) as count FROM Car WHERE DriverID = '$DriverID'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);
        if ($row['count'] <= 1) {
            throw new Exception('배송기사는 최소 하나 이상의 차량을 가져야 합니다.');
        }

        // 차량 정보 삭제
        $query = "DELETE FROM Car WHERE Model = '$Model'";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('차량 정보 삭제에 실패했습니다.');
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 삭제되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Car_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Car_list.php'>";
    }

    mysqli_close($conn);
}
?>
