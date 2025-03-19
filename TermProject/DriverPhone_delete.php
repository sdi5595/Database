<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DriverID = $_POST['DriverID'];
    $PhoneNumber = $_POST['PhoneNumber'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    $query = "DELETE FROM DriverPhone WHERE DriverID = '$DriverID' AND PhoneNumber = '$PhoneNumber'";
    if (mysqli_query($conn, $query)) {
        mysqli_query($conn, "commit");
        msg('성공적으로 삭제되었습니다.');
    } else {
        mysqli_query($conn, "rollback");
        msg('삭제에 실패했습니다: ' . mysqli_error($conn));
    }

    mysqli_close($conn);
    echo "<meta http-equiv='refresh' content='0;url=DriverPhone_form.php'>";
}
?>
