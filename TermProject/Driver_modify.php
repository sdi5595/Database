<?php
include "config.php";    // 데이터베이스 연결 설정 파일
include "util.php";      // 유틸 함수

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $DriverID = $_POST['DriverID'];
    $DriverName = $_POST['DriverName'];
    $Area = $_POST['Area'];
    $CarModels = $_POST['CarModels'];
    $CarMileages = $_POST['CarMileages'];

    $conn = dbconnect($host, $dbid, $dbpass, $dbname);

    // 트랜잭션 시작
    mysqli_query($conn, "set autocommit = 0");
    mysqli_query($conn, "set session transaction isolation level serializable");
    mysqli_query($conn, "begin");

    try {
        // 배송기사 정보 수정
        $query = "UPDATE DeliveryDriver SET DriverName = '$DriverName', Area = '$Area' WHERE DriverID = '$DriverID'";
        if (mysqli_query($conn, $query)) {
            // 기존 차량 정보 삭제
            $delete_car_query = "DELETE FROM Car WHERE DriverID = '$DriverID'";
            mysqli_query($conn, $delete_car_query);

            // 새로운 차량 정보 등록
            $car_models = explode(',', $CarModels);
            $car_mileages = explode(',', $CarMileages);
            if (count($car_models) != count($car_mileages)) {
                throw new Exception('차량 모델명과 연비의 개수가 일치하지 않습니다.');
            }
            for ($i = 0; $i < count($car_models); $i++) {
                $model = trim($car_models[$i]);
                $mileage = trim($car_mileages[$i]);
                $car_query = "INSERT INTO Car (DriverID, Model, Mileage) VALUES ('$DriverID', '$model', '$mileage')";
                mysqli_query($conn, $car_query);
            }

            // 트랜잭션 커밋
            mysqli_query($conn, "commit");
            echo '<script>
                    alert("성공적으로 수정되었습니다.");
                    window.location.href = "Driver_list.php";
                  </script>';
        } else {
            // 트랜잭션 롤백
            mysqli_query($conn, "rollback");
            msg('수정에 실패했습니다: ' . mysqli_error($conn));
            echo "<meta http-equiv='refresh' content='0;url=Driver_form.php?DriverID={$DriverID}'>";
        }
    } catch (Exception $e) {
        // 트랜잭션 롤백
        mysqli_query($conn, "rollback");
        msg('수정에 실패했습니다: ' . mysqli_error($conn));
        echo "<meta http-equiv='refresh' content='0;url=Driver_form.php?DriverID={$DriverID}'>";
    }

    mysqli_close($conn);
}
