<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Model = $_POST['Model'];
    $Mileage = $_POST['Mileage'];
    $DriverID = $_POST['DriverID'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 배송기사ID가 유효한지 확인
        $query = "SELECT * FROM DeliveryDriver WHERE DriverID = '$DriverID'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) == 0) {
            throw new Exception('유효한 배송기사ID가 아닙니다.');
        }

        // 차량 정보 등록
        $query = "INSERT INTO Car (Model, Mileage, DriverID) VALUES ('$Model', '$Mileage', '$DriverID')";
        if (!mysqli_query($conn, $query)) {
            throw new Exception('차량 정보 등록에 실패했습니다.');
        }

        // 트랜잭션 커밋
        mysqli_query($conn, "commit");

        s_msg('성공적으로 추가되었습니다.');
        echo "<meta http-equiv='refresh' content='0;url=Car_list.php'>";
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg($e->getMessage());
        echo "<meta http-equiv='refresh' content='0;url=Car_form.php'>";
    }

    mysqli_close($conn);
}
?>
