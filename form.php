<?php

function pp($txt)
{
    echo '<pre>';
    print_r($txt);
    echo '</pre>';
}

function getCarsFromAPI()
{
	// get cURL resource
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	// set url
	curl_setopt($ch, CURLOPT_URL, 'https://www.coppermineslodge.com/API/cars/');

	// set method
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	// return the transfer as a string
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// send the request and save response to $response
	$response = curl_exec($ch);

	// stop if fails
	if (!$response) {
		die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
	}

	// close curl resource to free up system resources 
	curl_close($ch);

	return json_decode($response);
}
$cars = getCarsFromAPI();


// $cars = file_get_contents("cars.json"); //gets entire contents of "cars.json"

// $cars = json_decode($cars);

pp($cars); 
exit();

$filterByYear = isset($_GET['filterByYear']) ? $_GET['filterByYear'] : null;
$searchModel = isset($_GET['searchModel']) ? $_GET['searchModel'] : null;
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : null;

if ($filterByYear) {
    $filteredCars = [];
    foreach ($cars as $car) {
        $carYear = date('Y', strtotime($car->Year)); // Extract the year from the car's Year property
        if ($carYear == $filterByYear) {
            $filteredCars[] = $car;
        }
    }
    $cars = $filteredCars;
}


if ($sortBy) {
    usort($cars, function ($car1, $car2) {
        $sortBy = $_GET['sortBy'];
        $sortOrder = $_GET['Order'];

        if ($sortOrder == 'Desc') {
            return $car1->$sortBy < $car2->$sortBy;
        } else {
            return $car1->$sortBy > $car2->$sortBy;
        }
    });
}

if ($searchModel) {
    $filteredCars = [];
    foreach ($cars as $car) {
        if (stripos($car->Name, $searchModel) !== false) {
            $filteredCars[] = $car;
        }
    }
    $cars = $filteredCars;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>GitCar</title>
    <style>
        body,
        html {
            letter-spacing: 2px;
            background-color: #181818;
            color: #F5F5F5;
            font-family: 'Roboto', sans-serif;
        }

        .card {
            box-shadow: 0px 0px 10px 2px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-flow: row wrap;
            width: 90%;
            padding: 30px;
            max-width: 1200px;
            margin: 50px auto;
            border-radius: 15px;
            background-color: #303030;
            gap: 40px;
            justify-content: space-around;
        }

        .card h1 {
            text-decoration: underline;
        }

        .data {
            box-shadow: 0px 0px 5px 2px rgba(0, 0, 0, 0.2);
            background-color: #404040;
            width: 100%;
            max-width: 280px;
            padding: 20px;
            border: 1px solid #444;
            border-radius: 15px;
            margin: 10px auto;
        }

        .data span {
            font-weight: bold;
            font-size: 1.1em;
            font-family: sans-serif, Arial, Helvetica;
            display: inline-block;
            margin: 5px 0;
        }

        .mpg-1 {
            color: #E53935;
        }

        .mpg-2 {
            color: #FB8C00;
        }

        .mpg-3 {
            color: #1E88E5;
        }

        .mpg-4 {
            color: #8E24AA;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        select,
        input[type="text"] {
            padding: 5px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #404040;
            color: #F5F5F5;
        }

        input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #1E88E5;
            color: #F5F5F5;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0d47a1;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>GitCar</h1>
    </div>
    <div class="card">
        <form action="">
            <label for="filterByYear">Filter By (Model Year)</label>
            <select name="filterByYear">
                <?php
                // Specify the keys you're interested in
                $modelYears = ['1971', '1972', '1973', '1974', '1975', '1976', '1977', '1978', '1979', '1980', '1981', '1982'];
                echo '<option value=""></option>';
                foreach ($modelYears as $modelYear) {

                    echo '<option value="' . $modelYear . '">' . $modelYear . '</option>';
                }
                ?>
            </select>
            <label for="sortBy">Sort By</label>
            <select name="sortBy">
                <?php
                // Specify the keys you're interested in
                $data_keys = ['Name', 'Miles_per_Gallon', 'Horsepower', 'Cylinders', 'Displacement', 'Weight_in_lbs', 'Acceleration', 'Year', 'Origin'];
                echo '<option value=""></option>';

                foreach ($data_keys as $data_key) {
                    echo "<option value='" . $data_key . "'>" . str_replace("_", " ", $data_key) . "</
                    option>";
                }
                ?>
            </select>
            <div class="Order">
                <label for="Order">Sort Order</label>
                <select name="Order">
                    <option value=""></option>
                    <option value="Asc" <?php echo ($_GET['Order'] == 'Asc') ? 'selected' : ' '; ?>>Ascending</option>
                    <option value="Desc" <?php echo ($_GET['Order'] == 'Desc') ? 'selected' : ' '; ?>>Descending</option>
                </select>
            </div>
            <label for="searchModel">Search Model Name</label>
            <input type="text" name="searchModel">
            <input type="submit" value="Search">
        </form>
    </div>
    <div class="card">
        <?php foreach ($cars as $car) {

            //Determine how old car is from today's year
            // Get the current year.
            $currentYear = (new DateTime())->format('Y-m-d');

            // Get the year from the car's Year property.
            $carYear = (new DateTime($car->Year))->format('Y-m-d');

            // Calculate how many years old the car is.
            $carAge = $currentYear - $carYear;

            // Determine MPG color group
            switch (true) {
                case ($car->Miles_per_Gallon < 15):
                    $mpg_color_class = 'mpg-1';
                    break;
                case ($car->Miles_per_Gallon >= 15 && $car->Miles_per_Gallon < 19):
                    $mpg_color_class = 'mpg-2';
                    break;
                case ($car->Miles_per_Gallon >= 19 && $car->Miles_per_Gallon < 20):
                    $mpg_color_class = 'mpg-3';
                    break;
                default:
                    $mpg_color_class = 'mpg-4';
            }

            // Determine flag emoji
            switch ($car->Origin) {
                case "USA":
                    $flag = "🇺🇸";
                    break;
                case "Europe":
                    $flag = "🇪🇺";
                    break;
                case "Japan":
                    $flag = "🇯🇵";
                    break;
                default:
                    $flag = "";
            }

            echo '<div class="data">';
            echo '<span>Name: ' . $car->Name . '</span>';
            echo '<span>HorsePower to Weight Ratio: ' . round($car->Horsepower / $car->Weight_in_lbs, 4) . '</span>';
            echo '<span>HorsePower: ' . $car->Horsepower . '</span>';
            echo '<br>';
            echo '<span class="' . $mpg_color_class . '">Miles Per Gallon: ' . $car->Miles_per_Gallon . '</span>';
            echo '<br>';
            echo '<span>Year: ' . $car->Year . ' (' . $carAge . ' years old)</span>';
            echo '<br>';
            echo '<span >Origin: ' . $flag . ' ' . $car->Origin . '</span>';
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>