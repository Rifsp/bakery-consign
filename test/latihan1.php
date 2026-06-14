<?php
// $nama="John Doe";
// $umur=25; 

// $buah = ["apel", "mangga", "jeruk", "pisang"];

// echo "Nama: " . $nama . "<br>";
// echo "Umur: " . $umur . "<br>";

// echo "<ul>";
//  foreach ($buah as $item) {
//    echo "<li>$item</li>";
//  }
// echo "</ul>";


// no3

// $nilai = isset($_POST['nilai']) ? $_POST['nilai'] : 0;

// function cekNilai($nilai) {
//     if ($nilai >= 85) {
//         return "A";
//     } elseif ($nilai >= 70) {
//         return "B";
//     } elseif ($nilai >= 55) {
//         return "C";
//     } elseif ($nilai < 55 ) {
//         return "D";
//     }

// }

// echo "Nilai: " . $nilai . "<br>";
// echo "Grade: " . cekNilai($nilai);
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : ''; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
 echo"Terima kasih, " . $nama . "! Email Anda adalah " . $email . " telah diterima.";
}

 ?>


<form method="post" action="">
<input type="text" name="nama" placeholder="Nama">
<input type="email" name="email" placeholder="Email">
<input type="submit" value="Submit">
</form>










