<?php 

include "connect.php";

require_once 'Classes/PHPExcel/IOFactory.php';

if(isset($_POST['fileUpload'])){ 
    $fileInfo = pathinfo($_FILES['excel_file']['name']);
    
    $spreadsheet = PHPExcel_IOFactory::load($_FILES['excel_file']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();  
    $worksheet_arr = $worksheet->toArray(); 

    unset($worksheet_arr[0]); 

    foreach($worksheet_arr as $row){ 
        $name = $row[0]; 
        $department = $row[1]; 

        $insertQuery = "INSERT INTO employee(name, department) VALUES(?,?)";

        $stmt1 = $conn->prepare($insertQuery);
        $stmt1->bind_param('ss', $name, $department);

        $stmt1->execute();
    }
}

if(isset($_POST['deleteNames'])) {
    // Confirmation received, proceed with deletion
    $query = "DELETE FROM `employee`";
    if($conn->query($query)) {
        // Deletion successful
        echo "<script>alert('All records have been deleted.')</script>";
    } else {
        // Error occurred while deleting
        echo "<script>alert('Error: Unable to delete records.')</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raffle System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style type="text/css">
        body {
            font-family: Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #5586B1;
        }

        #container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #5586B1;
            color: white;
        }

        #chart {
            width: 80%;
            max-width: 600px;
            height: auto;
            margin-bottom: 20px;
        }

        #spinButton {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            font-size: 16px;
            cursor: pointer;
            background-color: #0061BC;
            color: white;
            border: none;
            outline: none;
            margin-bottom: 20px;
        }

        #winners {
            width: 80%;
            max-width: 600px;
            background-color: #94C1FA;
            padding: 20px;
            overflow-y: auto;
            margin-top: 20px;
            color: white;

        }

        #winners h2 {
            margin-top: 0;
            color: white;
        }

        #winnerNamesTable {
            width: 100%;
            background-color: #94C1FA;
            border-collapse: collapse;
            color: white;
        }

        #winnerNamesTable td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ffffff;
            color: white;
        }

        #addNamesButton {
            color: white;
            background-color: #0061BC;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        #addNamesButton:hover {
            background-color: #003D9C;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        #confetti {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }

        #curtain {
            position: relative;
            width: 80%;
            max-width: 600px;
            height: 280px;
            margin: auto;
            box-shadow: 0px 0px 6px 2px #003B83;
            background: #FFF;
            text-align: center;
            padding-top: 50px;
        }

        #curtain img {
            position: absolute;
            top: 0px;
            height: 294px;
            width: 50%;
        }

        #leftCurtain {
            left: 0;
        }

        #rightCurtain {
            right: 0;
        }
        #leftcon {
            left: 0;
        }

        #rightcon {
            right: 0;
        }

        #curtain_buttons {
            text-align: center;
            margin-top: 20px;
        }

        #curtain_buttons input[type="button"] {
            margin: 10px;
            width: 150px;
            height: 45px;
            border-radius: 5px;
            color: white;
            background-color: #0061BC;
            border: none;
            cursor: pointer;
        }

        #curtain_buttons input[type="button"]:hover {
            background-color: #003D9C;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<!-- spin button -->
<body background="color.jpg">
    <div id="container">
        <div id="chart"></div>
        <button id="spinButton">PRESS TO SPIN</button>
        <button id="addNamesButton">Add Names</button>
        <form method="post">
            <button type="submit" name="deleteNames">Delete All Names</button>
        </form>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="excel_file" id="excel_file">
            <input type="submit" value="Upload Excel File" name="fileUpload">
        </form>
        
        <!-- <div id="winners">
            <h2>Winners List</h2>
            <table id="winnerNamesTable">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Timestamp</td>
                    </tr>
                </thead>
                <tbody id="winnerNamesBody"></tbody>
            </table>
        </div> -->

    </div>
    <!-- Confetti GIF -->
   
    <div id="curtain">
        <h1 id="winnerInsideCurtain"></h1>
         <!-- <img src="confetti.gif" alt="confetti" class="confetti"> -->
         <img id="leftcon" src="confetti.gif">
        <img id="rightcon" src="confetti.gif">
        <img id="leftCurtain" src="curtain.jpg">
        <img id="rightCurtain" src="curtain.jpg">
    </div>
    <div id="curtain_buttons">
        <input type="button" value="OPEN CURTAIN" onclick="openCurtain()">
        <input type="button" value="CLOSE CURTAIN" onclick="closeCurtain()">
    </div>
    <div id="container">
        <div id="winners">
            <h2>Winners List</h2>
            <table id="winnerNamesTable">
                <thead>
                    <tr>
                        
                        <td>Name</td>
                        <td>Timestamp</td>
                    </tr>
                </thead>
                <tbody id="winnerNamesBody"></tbody>
            </table>

        </div>
        <button id="saveWinnerButton">Save</button>

    </div>
    
    <script src="https://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        var padding = { top: 20, right: 40, bottom: 0, left: 0 },
            w = 600,
            h = 400,
            r = Math.min(w, h) / 2,
            rotation = 0,
            oldrotation = 0,
            picked = 105000,
            oldpick = [],
            color = d3.scale.category20();

        var data = [];

        var svg = d3.select('#chart')
            .append("svg")
            .data([data])
            .attr("width", w + padding.left + padding.right)
            .attr("height", h + padding.top + padding.bottom);

        var container = svg.append("g")
            .attr("class", "chartholder")
            .attr("transform", "translate(" + (w / 2 + padding.left) + "," + (h / 2 + padding.top)+ ")");

            var vis= container.append("g");

        var pie = d3.layout.pie().sort(null).value(function (d) { return 1; });

        var arc = d3.svg.arc().outerRadius(r);

        var updateWheel = function () {
            vis.selectAll("g.slice").remove();

            var arcs = vis.selectAll("g.slice")
                .data(pie(data))
                .enter()
                .append("g")
                .attr("class", "slice");

            arcs.append("path")
                .attr("fill", function (d, i) { return color(i); })
                .attr("d", function (d) { return arc(d); });

            arcs.append("text").attr("transform", function (d) {
                d.innerRadius = 0;
                d.outerRadius = r;
                d.angle = (d.startAngle + d.endAngle) / 2;
                return "rotate(" + (d.angle * 180 / Math.PI - 90) + ")translate(" + (d.outerRadius - 10) + ")";
            })
                .attr("text-anchor", "end")
                .text(function (d) {
                    return d.data.label;
                });
        };

        /*------add names function / dialog box*/

        var inputNames = function () {
            var name = prompt("Enter a name");
            while (name !== null && name.trim() !== "") {

                $.ajax({
                    type: "POST",
                    url: "insertName.php", // Update the URL to point to your PHP script
                    data: { name: name },
                    success: function (response) {
                        // Handle success response if needed
                        location.reload();
                        updateWheel();
                    },
                    error: function () {
                        alert("Error inserting name into the database.");
                    }
                });

                name = prompt("Enter another name (or click 'Cancel' to stop)");
            }
        };


        document.getElementById("addNamesButton").addEventListener("click", function () {
            inputNames();
        });
        // -----------save winner button--------------

        document.getElementById("saveWinnerButton").addEventListener("click", function () {
            // Get the winner's name
            var winnerName = document.getElementById("winnerInsideCurtain").textContent.trim();

            //--------------- Check if the winner's name is not empty
            if (winnerName !== "") {
                // Send an AJAX request to save the winner's data
                $.ajax({
                    type: "POST",
                    url: "save.php", // Update the URL to point to your PHP script
                    data: { name: winnerName },
                    success: function (response) {
                        alert(response); // Display the response from the PHP script
                    },
                    error: function () {
                        alert("Error saving winner's data.");
                    }
                });
            } else {
                alert("No winner selected.");
            }
        });


        var spinWheel = function () {
            container.on("click", null);

            if (oldpick.length == data.length) {
                container.on("click", null);
                return;
            }

            var ps = 360 / data.length,
                rng = Math.floor((Math.random() * 1440) + 360);

            rotation = (Math.round(rng / ps) * ps);

            picked = Math.round(data.length - (rotation % 360) / ps);
            picked = picked >= data.length ? (picked % data.length) : picked;

            if (oldpick.indexOf(picked) !== -1) {

                spinWheel();
                return;
            } else {
                oldpick.push(picked);
            }

            rotation += 90 - Math.round(ps / 2);

            vis.transition()
                .duration(3000)
                .attrTween("transform", rotTween)
                .each("end", function () {
                    oldrotation = rotation;

                    var winnerName = data[picked].label;

                    // Add the winner to the winner's list
                    var winnersDiv = document.getElementById("winnerNamesBody");
                    var currentTime = new Date();
                    var timestamp = currentTime.toLocaleString();
                    var winnerTimestamp = timestamp;

                    var newRow = winnersDiv.insertRow();
                    var cell1 = newRow.insertCell(0);
                    var cell2 = newRow.insertCell(1);
                    cell1.innerHTML = winnerName;
                    cell2.innerHTML = winnerTimestamp;

                    // Remove the selected participant from the data
                    data.splice(picked, 1);
                    updateWheel();

                    container.on("click", spinWheel);

                    // Show winner inside the curtain
                    var audio = new Audio('drumroll2.mp3');
            audio.play();
                    document.getElementById("winnerInsideCurtain").textContent = "Congratulations, " + winnerName + "! You are a winner!";
                });
        };

        document.getElementById("spinButton").addEventListener("click", function () {
            // Play the spin audio
            var audio = new Audio('spin.mp3');
            audio.play();

            // Start spinning the wheel
            spinWheel();
        });

        var rotTween = function (to) {
            var i = d3.interpolate(oldrotation % 360, rotation);
            return function (t) {
                return "rotate(" + i(t) + ")";
            };
        };

        function openCurtain() {
            var audio = new Audio('clap.mp3');
            audio.play();
            $("#leftCurtain").animate({ width: 0 }, 1000);
            $("#rightCurtain").animate({ width: 0 }, 1000);
        }

        function closeCurtain() {

            $("#leftCurtain").animate({ width: "50%" }, 1000);
            $("#rightCurtain").animate({ width: "50%" }, 1000);
        }

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Parse JSON response
                var employees = JSON.parse(this.responseText);
                
                // Process the data
                employees.forEach(function(employee) {
                // Access employee properties, for example:
                console.log(employee.emp_id + ", " + employee.name);
                data.push({ label: employee.name, value: 1, question: "Congrats! " + employee.name });
                });

                updateWheel();
            }
        };
        xhttp.open("GET", "loadName.php", true);
        xhttp.send();
    </script>
</body>
</html>
