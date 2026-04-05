<?php
// Fix FPDF Font Issues
echo "<h2>🔧 Fix FPDF Font Path Issues</h2>";

// Check if FPDF directory exists
$fpdf_dir = __DIR__ . '/lib/fpdf';
$font_dir = $fpdf_dir . '/font';

echo "<h3>📁 Directory Check:</h3>";

if (file_exists($fpdf_dir)) {
    echo "<p style='color: green;'>✅ FPDF directory found: $fpdf_dir</p>";
} else {
    echo "<p style='color: red;'>❌ FPDF directory not found: $fpdf_dir</p>";
}

if (file_exists($font_dir)) {
    echo "<p style='color: green;'>✅ Font directory found: $font_dir</p>";
} else {
    echo "<p style='color: red;'>❌ Font directory not found: $font_dir</p>";
}

// Check for font files
$required_fonts = ['helvetica.php', 'helveticab.php', 'helveticabi.php', 'helveticai.php', 'courier.php', 'times.php'];
$missing_fonts = [];

foreach ($required_fonts as $font_file) {
    $font_path = $font_dir . '/' . $font_file;
    if (file_exists($font_path)) {
        echo "<p style='color: green;'>✅ Font file found: $font_file</p>";
    } else {
        echo "<p style='color: red;'>❌ Font file missing: $font_file</p>";
        $missing_fonts[] = $font_file;
    }
}

if (!empty($missing_fonts)) {
    echo "<h3>🛠️ Creating Missing Font Files:</h3>";
    
    // Create font directory if it doesn't exist
    if (!file_exists($font_dir)) {
        mkdir($font_dir, 0755, true);
        echo "<p style='color: blue;'>📁 Created font directory: $font_dir</p>";
    }
    
    // Create basic font definitions
    $font_definitions = [
        'helvetica.php' => '<?php
$fpdf_charwidths[\'helvetica\']=array(
    chr(0)=>0,chr(1)=>0,chr(2)=>0,chr(3)=>0,chr(4)=>0,chr(5)=>0,chr(6)=>0,chr(7)=>0,chr(8)=>0,chr(9)=>0,chr(10)=>0,chr(11)=>0,chr(12)=>0,chr(13)=>0,chr(14)=>0,chr(15)=>0,chr(16)=>0,chr(17)=>0,chr(18)=>0,chr(19)=>0,chr(20)=>0,chr(21)=>0,chr(22)=>0,chr(23)=>0,chr(24)=>0,chr(25)=>0,chr(26)=>0,chr(27)=>0,chr(28)=>0,chr(29)=>0,chr(30)=>0,chr(31)=>0,chr(32)=>278,chr(33)=>278,chr(34)=>355,chr(35)=>556,chr(36)=>556,chr(37)=>889,chr(38)=>667,chr(39)=>191,chr(40)=>333,chr(41)=>333,chr(42)=>389,chr(43)=>584,chr(44)=>278,chr(45)=>333,chr(46)=>278,chr(47)=>278,chr(48)=>556,chr(49)=>556,chr(50)=>556,chr(51)=>556,chr(52)=>556,chr(53)=>556,chr(54)=>556,chr(55)=>556,chr(56)=>556,chr(57)=>556,chr(58)=>278,chr(59)=>278,chr(60)=>556,chr(61)=>556,chr(62)=>556,chr(63)=>556,chr(64)=>1015,chr(65)=>667,chr(66)=>667,chr(67)=>722,chr(68)=>722,chr(69)=>667,chr(70)=>611,chr(71)=>778,chr(72)=>722,chr(73)=>278,chr(74)=>500,chr(75)=>667,chr(76)=>556,chr(77)=>833,chr(78)=>722,chr(79)=>778,chr(80)=>667,chr(81)=>778,chr(82)=>722,chr(83)=>667,chr(84)=>611,chr(85)=>722,chr(86)=>667,chr(87)=>944,chr(88)=>667,chr(89)=>667,chr(90)=>611,chr(91)=>278,chr(92)=>278,chr(93)=>278,chr(94)=>469,chr(95)=>556,chr(96)=>333,chr(97)=>556,chr(98)=>556,chr(99)=>500,chr(100)=>556,chr(101)=>556,chr(102)=>278,chr(103)=>556,chr(104)=>556,chr(105)=>222,chr(106)=>222,chr(107)=>500,chr(108)=>222,chr(109)=>833,chr(110)=>556,chr(111)=>556,chr(112)=>556,chr(113)=>556,chr(114)=>333,chr(115)=>500,chr(116)=>278,chr(117)=>556,chr(118)=>500,chr(119)=>722,chr(120)=>500,chr(121)=>500,chr(122)=>500,chr(123)=>334,chr(124)=>260,chr(125)=>334,chr(126)=>584);
$fpdf_charwidths[\'helvetica\'][chr(127)]=0;
$fpdf_charwidths[\'helvetica\'][chr(128)]=0;
$fpdf_charwidths[\'helvetica\'][chr(129)]=0;
$fpdf_charwidths[\'helvetica\'][chr(130)]=0;
$fpdf_charwidths[\'helvetica\'][chr(131)]=0;
?>',
        
        'helveticab.php' => '<?php
$fpdf_charwidths[\'helveticab\']=$fpdf_charwidths[\'helvetica\'];
?>',
        
        'helveticabi.php' => '<?php
$fpdf_charwidths[\'helveticabi\']=$fpdf_charwidths[\'helvetica\'];
?>',
        
        'helveticai.php' => '<?php
$fpdf_charwidths[\'helveticai\']=$fpdf_charwidths[\'helvetica\'];
?>'
    ];
    
    foreach ($font_definitions as $font_file => $content) {
        $font_path = $font_dir . '/' . $font_file;
        if (!file_exists($font_path)) {
            file_put_contents($font_path, $content);
            echo "<p style='color: green;'>✅ Created font file: $font_file</p>";
        }
    }
}

echo "<h3>🔧 Alternative Solutions:</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";
echo "<h4>Option 1: Use Arial instead of Helvetica</h4>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 4px;'>";
echo "// In generate_medical_pdf.php
\$pdf->SetFont('Arial', 'B', 15);  // Instead of Helvetica
\$pdf->SetFont('Arial', '', 12);   // Instead of Helvetica";
echo "</pre>";

echo "<h4>Option 2: Download Complete FPDF</h4>";
echo "<p>Download the complete FPDF library from: <a href='http://www.fpdf.org/en/dl.php?v=17&f=zip' target='_blank'>http://www.fpdf.org</a></p>";
echo "<p>Extract and replace the lib/fpdf folder completely.</p>";

echo "<h4>Option 3: Use TCPDF (Alternative)</h4>";
echo "<p>Consider using TCPDF instead of FPDF for better font support:</p>";
echo "<pre style='background: #fff; padding: 10px; border-radius: 4px;'>";
echo "// Install via composer or download from:
// https://github.com/tecnickcom/TCPDF";
echo "</pre>";
echo "</div>";

echo "<h3>🧪 Test FPDF:</h3>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 8px;'>";
echo "<p>After fixing fonts, test the PDF generation:</p>";
echo "<ol>";
echo "<li>Try generating a medical record PDF</li>";
echo "<li>Check if the error is resolved</li>";
echo "<li>Verify the PDF opens correctly</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='MedicalRecordsHistory.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>← Back to Medical Records</a></p>";
?>
