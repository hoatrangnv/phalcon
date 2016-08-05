<?php
namespace ITECH\Datasource\Lib;

class Captcha
{
    /**
     * @author Cuong.Bui
     */
    public function create(array $color, array $background, $font_dir, $captcha_dir)
    {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';
        $allowed_symbols = '012345679';
        $fontsdir = $font_dir;
        $length = 3;
        $width = 100;
        $height = 50;

        $fluctuation_amplitude = 0;
        $no_spaces = false;
        $show_credits = false;
        $credits = '';

        $foreground_color = $color; //array(59, 89, 152);
        $background_color = $background; //array(255, 255, 255);
        $jpeg_quality = 90;
        $fontsdir_absolute = $fontsdir;

        $handle = opendir($fontsdir_absolute);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if (preg_match('/\.png$/i', $file)) {
                    $fonts[] = $fontsdir_absolute . $file;
                }
            }
            closedir($handle);
        }

        $alphabet_length = strlen($alphabet);

        while (true) {
            while (true) {
                $keystring = '';

                for ($i = 0; $i < $length; $i++) {
                    $keystring .= $allowed_symbols{mt_rand(0, strlen($allowed_symbols) - 1)};
                }

                if (!preg_match('/cp|cb|ck|c6|c9|rn|rm|mm|co|do|cl|db|qp|qb|dp/', $keystring)) {
                    break;
                }
            }

            $font_file = $fonts[mt_rand(0, count($fonts) - 1)];
            $font = imagecreatefrompng($font_file);

            imagealphablending($font, true);

            $fontfile_width = imagesx($font);
            $fontfile_height = imagesy($font) - 1;

            $font_metrics = array();
            $symbol = 0;
            $reading_symbol = false;

            for ($i = 0; $i < $fontfile_width && $symbol < $alphabet_length; $i++) {
                $transparent = (imagecolorat($font, $i, 0) >> 24) == 127;

                if (!$reading_symbol && !$transparent) {
                    $font_metrics[$alphabet{$symbol}] = array('start' => $i);
                    $reading_symbol = true;
                    continue;
                }

                if ($reading_symbol && !$transparent) {
                    $font_metrics[$alphabet{$symbol}]['end'] = $i;
                    $reading_symbol = true;
                    continue;
                }

                if ($reading_symbol && $transparent) {
                    $font_metrics[$alphabet{$symbol}]['end'] = $i;
                    $reading_symbol = false;
                    $symbol++;
                    continue;
                }
            }

            $img = imagecreatetruecolor($width, $height);
            imagealphablending($img, true);

            $white = imagecolorallocate($img, 255, 255, 255);
            //$black = imagecolorallocate($img, 0, 0, 0);

            imagefilledrectangle($img, 0, 0, $width - 1, $height - 1, $white);

            $x = 1;

            for ($i = 0; $i < $length; $i++) {
                $m = $font_metrics[$keystring{$i}];
                $y = mt_rand(-$fluctuation_amplitude, $fluctuation_amplitude) + ($height - $fontfile_height) / 2 + 2;

                if ($no_spaces) {
                    $shift = 0;

                    if ($i > 0) {
                        $shift = 1000;

                        for ($sy = 7; $sy < $fontfile_height - 20; $sy += 1) {
                            for ($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1) {
                                $rgb = imagecolorat($font, $sx, $sy);
                                $opacity = $rgb >> 24;

                                if ($opacity < 127) {
                                    $left = $sx - $m['start'] + $x;
                                    $py = $sy + $y;

                                    if ($py > $height) {
                                        break;
                                    }

                                    for ($px = min($left, $width - 1); $px > $left - 12 && $px >= 0; $px -= 1) {
                                        $color = imagecolorat($img, $px, $py) & 0xff;

                                        if ($color + $opacity < 190) {
                                            if ($shift > $left - $px) {
                                                $shift = $left - $px;
                                            }

                                            break;
                                        }
                                    }

                                    break;
                                }
                            }
                        }

                        if ($shift == 1000) {
                            $shift = mt_rand(4, 6);
                        }
                    }
                } else {
                    $shift = -10;
                }

                imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontfile_height);
                $x += $m['end'] - $m['start'] - $shift;
            }

            if ($x < $width - 10) {
                break;
            }
        }

        $center = $x / 2;
        $img2 = imagecreatetruecolor($width, $height + ($show_credits ? 15 : 0));
        $foreground = imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
        $background = imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);

        imagefilledrectangle($img2, 0, $height, $width - 1, $height + 14, $foreground);
        imagestring($img2, 2, $width / 2 - imagefontwidth(2) * strlen($credits) / 2, $height, $credits, $background);

        $rand1 = mt_rand(750000, 1200000) / 10000000;
        $rand2 = mt_rand(750000, 1200000) / 10000000;
        $rand3 = mt_rand(750000, 1200000) / 10000000;
        $rand4 = mt_rand(750000, 1200000) / 10000000;
        $rand5 = mt_rand(0, 3141592) / 500000;
        $rand6 = mt_rand(0, 3141592) / 500000;
        $rand7 = mt_rand(0, 3141592) / 500000;
        $rand8 = mt_rand(0, 3141592) / 500000;
        $rand9 = mt_rand(330, 420) / 110;
        $rand10 = mt_rand(330, 450) / 110;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - $width / 2 + $center + 1;
                $sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;
                if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {
                    $color = 255;
                    $color_x = 255;
                    $color_y = 255;
                    $color_xy = 255;
                } else {
                    $color = imagecolorat($img, $sx, $sy) & 0xFF;
                    $color_x = imagecolorat($img, $sx + 1, $sy) & 0xFF;
                    $color_y = imagecolorat($img, $sx, $sy + 1) & 0xFF;
                    $color_xy = imagecolorat($img, $sx + 1, $sy + 1) & 0xFF;
                }

                if ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
                    $newred = $foreground_color[0];
                    $newgreen = $foreground_color[1];
                    $newblue = $foreground_color[2];
                } elseif ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
                    $newred = $background_color[0];
                    $newgreen = $background_color[1];
                    $newblue = $background_color[2];
                } else {
                    $frsx = $sx - floor($sx);
                    $frsy = $sy - floor($sy);
                    $frsx1 = 1 - $frsx;
                    $frsy1 = 1 - $frsy;
                    $newcolor = (
                        $color * $frsx1 * $frsy1 +
                        $color_x * $frsx * $frsy1 +
                        $color_y * $frsx1 * $frsy +
                        $color_xy * $frsx * $frsy
                    );

                    if ($newcolor > 255) {
                        $newcolor = 255;
                    }

                    $newcolor = $newcolor / 255;
                    $newcolor0 = 1 - $newcolor;
                    $newred = $newcolor0 * $foreground_color[0] + $newcolor * $background_color[0];
                    $newgreen = $newcolor0 * $foreground_color[1] + $newcolor * $background_color[1];
                    $newblue = $newcolor0 * $foreground_color[2] + $newcolor * $background_color[2];
                }

                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
            }
        }

        ob_start();
        if (function_exists('imagejpeg')) {
            //header('Content-Type: image/jpeg');
            imagejpeg($img2, null, $jpeg_quality);
        } elseif (function_exists('imagegif')) {
            //header('Content-Type: image/gif');
            imagegif($img2);
        } elseif (function_exists('imagepng')) {
            //header('Content-Type: image/x-png');
            imagepng($img2);
        }
        $image = ob_get_clean();

        $filename_prefix = md5($keystring);
        $c_file = $filename_prefix . '.jpg';

        $create_file = $captcha_dir . $c_file;
        if (!file_exists($create_file)) {
            $fh = fopen($create_file, 'w+');
            chmod($create_file, 0777);
            fwrite($fh, $image);
            fclose($fh);
        }

        return array(
            'captcha' => $keystring,
            'filename' => $c_file
        );
    }

    public static function genpass()
    {
        $vocales = 'AaEeIiOoUu13580';
        $consonantes = 'BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz24679';
        $r = '';

        for ($i = 0; $i < 4; $i++) {
            if ($i % 2) {
                $r .= $vocales{rand(0, strlen($vocales) - 1)};
            } else {
                $r .= $consonantes{rand(0, strlen($consonantes) - 1)};
            }
        }

        return $r;
    }

    public function randpass()
    {
        $randomPassword = '';

        for ($i = 0; $i < 5; $i++) {
            $randnumber = mt_rand(48, 120);
            while (($randnumber >= 58 && $randnumber <= 64) || ($randnumber >= 91 && $randnumber <= 96)) {
                $randnumber = mt_rand(48, 120);
            }
            $randomPassword .= chr($randnumber);
        }

        return $randomPassword;
    }
}