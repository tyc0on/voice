<?php
session_start();

//error on
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


include 'include.php';
$con = new mysqli($sqlh, $sqlu, $sqlp, $sqld);
include 'config.php';

if ($con->connect_errno) {
    printf("Connection failed: %s\n", $con->connect_error);
    exit();
}
function getColorFromLetter($letter)
{
    $ascii = ord(strtoupper($letter));

    $red = ($ascii * 23) % 256;
    $green = ($ascii * 47) % 256;
    $blue = ($ascii * 67) % 256;

    return "rgb($red, $green, $blue)";
}

$letterStyles = [];
foreach (range('A', 'Z') as $letter) {
    $letterStyles[$letter] = getColorFromLetter($letter);
}
function getLetterStyle($letter, $letterStyles)
{
    return $letterStyles[strtoupper($letter[0])] ?? null;
}

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

$query = "SELECT * FROM files WHERE active = 1";
if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR original_name LIKE '%$search%')";
}
$query .= " ORDER BY added_date DESC LIMIT 25 OFFSET $offset";

$result = $con->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $created_at = date('F j, Y', strtotime($row['added_date']));
        $original_name = strlen($row['original_name']) > 25 ? substr($row['original_name'], 0, 25) . '...' : $row['original_name'];
        $url = strlen($row['url']) > 25 ? substr($row['url'], 0, 25) . '...' : $row['url'];

        $checkbox = '';
        $copybutton = "<button class='btn btn-sm fw-bold btn-light copy-button ms-4 p-1 ps-2 pe-2' onclick='copyToClipboard(\"" . htmlspecialchars($row['url']) . "\", this)'>
        Copy to Clipboard
        </button>";
        $tags = '<span class="badge badge-light-success fw-bold px-4 py-3">Online</span>';

        $firstLetter = mb_substr($row['original_name'], 0, 1);

        $backgroundColor = getLetterStyle($firstLetter, $letterStyles);
        $icon = "<div class='me-5 position-relative'><div class='symbol symbol-35px symbol-circle'>
															   <span class='symbol-label' style='background-color: $backgroundColor; color: white; font-weight: bold;'>$firstLetter</span>
															</div></div>";

        echo '<tr>
															<td>' . $checkbox . '
																<div class="d-flex align-items-center">
																	' . $icon . '
																	<div class="d-flex flex-column justify-content-center">
																		<span href="" class="fs-6 text-gray-800 text-hover-primary">' . $original_name . '</span>
																		<div class="fw-semibold text-gray-400">' . $url . '</div>
																	</div>
																</div>
															</td>
															<td class="">
																<input type="hidden" id="name-' . $row['id'] . '" name="name-' . $row['id'] . '" value="' . $row['name'] . '">
																<a href="/run?url=' . urlencode($row['url']) . '&pitch=0" class="btn btn-primary btn-active-light-primary" id="selectButton-' . $row['id'] . '">Select -></a> 
															</td>';

        echo '<td class="fs-7" style="display: flex; align-items: center; gap: 10px;">';
        $pitches = [-16, -12, -8, -4, 0, 4, 8, 12, 16];

        $defaultFileName = "samples/" . $row['name'] . ".mp3";
        echo '<div style="float: left; margin-right: 10px;">';

        echo '<div id="playIcon-' . $row['id'] . '" onclick="playAudio(' . $row['id'] . ', \'' . $row['name'] . '\');" style="cursor: pointer; float: left; margin-right: 10px;">
													<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-play-fill" viewBox="0 0 16 16">
													<path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>
												  </svg>
</div>';
        echo '<audio id="audioPlayer-' . $row['id'] . '" data-row-name="' . $row['name'] . '" data-row-url="' . $row['url'] . '" style="height:40px; display:none;" controls>
<source type="audio/mpeg">
Your browser does not support the audio tag.
</audio>';
        echo '</div>';
        echo '</div>';
        echo '<div>Pitch:<br><select id="pitchSelector-' . $row['id'] . '" onchange="updateAudioSource(this.value, ' . $row['id'] . ', \'' . $row['name'] . '\');">';
        foreach ($pitches as $pitch) {
            if ($pitch == 0) {
                echo '<option value="' . $pitch . '" selected>' . $pitch . '</option>';
            } else {
                echo '<option value="' . $pitch . '">' . $pitch . '</option>';
            }
        }
        echo '</select>';
        echo '</div>';
        echo '<div>Gender:<br><button id="genderToggle-' . $row['id'] . '" onclick="toggleGender(' . $row['id'] . ');">Male</button></div>';


        echo '</td>
															<!--<td>
																' . $tags . '
															</td>-->
															
															<td class="text-end">' . $created_at . '</td>
														</tr>';
    }
} else {
    echo '<tr><td colspan="4">No more records found.</td></tr>';
}

$con->close();
