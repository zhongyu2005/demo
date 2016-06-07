<?php

/**
 * 二维码操作模型
 **/
class QrCodeModel extends Model
{

    /**
     * 获取字体大小
     */
    public function getFontSize($text)
    {
        $textLen = mb_strlen($text, "UTF-8");
        if ($textLen <= 6) {
            return 16;
        } elseif ($textLen > 6 && $textLen <= 8) {
            return 12;
        }elseif ($textLen > 8 && $textLen <= 9) {
             return 11;
        } elseif ($textLen > 9 && $textLen <= 11) {
             return 9;
        } else {
             return 8;
        }
    }
    
    //生成图片函数
    /**
     $style = array('font'=>字体文件,绝对路径
     'fontW' => 文字宽度//$fontSize * 1.315,
     'fontH' => 文字高度// $fontSize * 1.315,
     'pandding' => 左右间距//0,
     'size' => 字体大小//$fontSize,
     'model' => 1, //1居中无间距，2居中间距平分，3居左，4居右
     'x' => 文字x轴起始位置  null, 设置后model无效
     'y' => 156 - (getFontSize('你') - $fontSize)/2 //138 + getFontSize($text) + (18-getFontSize($text))
     );
     **/
    public function genImage ($imgUrl, $logoFile, $text, $saveFile, $style = array()) {
        $qrcFile = '/tmp/tmp.jpg';
        $qrcStr = file_get_contents($imgUrl);
        if (!$qrcStr) {
            return false;
        }
        file_put_contents($qrcFile, $qrcStr);
    
        //二维码图片的宽 高
        list($width, $height) = getimagesize($qrcFile);
        //字体背景 宽  高
        list($logoImg_width, $logoImg_height) = getimagesize($logoFile);
        //var_dump($logoImg_width, $logoImg_height);
        //$logoImg = imagecreatefrompng($logoFile);
        $logoImg = imagecreatefromstring(file_get_contents($logoFile));
        //imagesavealpha($logoImg, true);

        //添加文字
        $fontW = isset($style['fontW'])? $style['fontW'] : 40;
        $fontH = isset($style['fontH'])?  $style['fontH']: 40;
        $fontSize = isset($style['size'])?  $style['size']: 30;
        $textLen = mb_strlen($text, "UTF-8");
        $FW = $fontW * $textLen;
        //var_dump($logoImg_width, $logoImg_height, $FW, mb_strlen($text, "UTF-8"));
        //左右边距
        $lrW = isset($style['pandding']) ?$style['pandding'] : 5;
        //计算字体之间间隔宽度
        $dfW = ($logoImg_width - $FW - ($lrW*2)) / ($textLen+1);
        //var_dump('间间隔宽度'.$dfW);
        $textcolor = imagecolorallocate($logoImg,242, 242, 242); //设置水印字体颜色
        $font = isset($style['font']) ?$style['font'] : 'c:/windows/fonts/msyhbd.ttf'; //定义字体
        //$wh = 78;//汉字距离logo顶部距离
        if (isset($style['y']) && null !== $style['y']) {
            $wh = $style['y'];
        } else {
            $wh = (($logoImg_height+$fontSize)/2);
        }
        if (isset($style['x']) && null !== $style['x']) {//if x 1
            $wx = $style['x'];
            imagettftext($logoImg, $fontSize, 0, $wx, $wh, $textcolor, $font, $text);//将文字写到图片中
        } else { // if x else
            $fontModel = isset($style['model']) ?$style['model'] : 1;
            if ($fontModel == 1) {
                $wx = ($logoImg_width - $FW)/2 + $lrW;
                imagettftext($logoImg, $fontSize, 0, $wx, $wh, $textcolor, $font, $text);//将文字写到图片中
            } else if ($fontModel == 2) {
                for ($i = 1; $i <= $textLen; $i++) {
                    $str = mb_substr($text, $i-1, 1, "UTF-8");
                    //var_dump($str, '====');
                    $wx = (($i - 1) * $fontW) + ($i*$dfW) + $lrW;
                    //var_dump($wx, '----');
                    imagettftext($logoImg, $fontSize, 0, $wx, $wh, $textcolor, $font, $str);//将文字写到图片中
                }
            } else  {
    
            }
        } // if x else end
    
        //合拼图片
        $qrcImg = imagecreatefromjpeg($qrcFile);
        $x =  ($width - $logoImg_width) / 2;
        $y = ($height - $logoImg_height) / 2;
        imagecopyresampled($qrcImg,$logoImg,$x,$y,0,0,$logoImg_width, $logoImg_height, $logoImg_width, $logoImg_height);
        #imagepng($qrcImg, $saveFile);
        ob_end_clean();
        header('Content-type: image/png');
        imagepng($qrcImg);
        //imagejpeg($qrcImg, $saveFile);
        imagedestroy($logoImg);
        imagedestroy($qrcImg);
    
        unlink($qrcFile);
        return true;
    }

    /**
     * 根据规则美化二维码
     * @param $original 图片
     * @param $logo logo
     * @param $bg 背景图片
     * @param $name 文字
     */
    public function getActiveImage ($original,$logo,$bg,$name,$font=false) {
        $logo_f=WX_LOG.'act_logo.png';
        $bg_f=WX_LOG.'act_bg.jpg';
        $original_f=WX_LOG.'actorg_'.time().'.jpg';
        $font=empty($font) ? dirname(LIB_PATH).'/www/Public/css/MSYH.TTC' : $font;
        if(!is_file($logo_f)){
            file_put_contents($logo_f,file_get_contents($logo));
        }
        if(!is_file($bg_f)){
            file_put_contents($bg_f,file_get_contents($bg));
        }
        file_put_contents($original_f,file_get_contents($original));

        //处理文字
        $name_len=mb_strlen($name,'utf-8');
        list($fontSize,$fontWidth)=$this->getTextSize($name_len);


        //得到微信二维码
        $qrcImg=$this->cut_jpg($original_f);
        $width=$height=290;

        list($logoImg_width, $logoImg_height) = getimagesize($logo_f);
        $logoImg = imagecreatefromstring(file_get_contents($logo_f));

        $textColor = imagecolorallocate($qrcImg,102, 91, 76); //设置水印字体颜色

        $textLen=(320 - ($fontWidth*$name_len))/2;
        $textLen=$textLen <=0 ? 0 : $textLen;

        $x =  ($width - $logoImg_width) / 2;
        $y = ($height - $logoImg_height) / 2;
        //logo的合并
        imagecopyresampled($qrcImg,$logoImg,$x,$y,0,0,$logoImg_width, $logoImg_height, $logoImg_width, $logoImg_height);
        //增加bg的写入
        $bgImg=imagecreatefromstring(file_get_contents($bg_f));
        imagecopyresampled($bgImg,$qrcImg,40,65,0,0,320,300,$width,$height);
        //增加文字的写入
        imagettftext($bgImg,$fontSize,0,40+$textLen,50,$textColor,$font,$name);
        header('Content-type: image/png');
        imagepng($bgImg);
        //clean
        imagedestroy($logoImg);
        imagedestroy($qrcImg);
        imagedestroy($bgImg);
        unlink($original_f);
        return true;
    }

    /**
     * 处理文字
     */
    public function getTextSize($name_len){
        $fontSize=20;
        $fontWidth=32;
        if($name_len>10 && $name_len<=12){
            $fontSize=20;
            $fontWidth=32;
        }elseif($name_len<=15){
            $fontSize=16;
            $fontWidth=21;
        }elseif($name_len<=20){
            $fontSize=12;
            $fontWidth=16;
        }elseif($name_len<=24){
            $fontSize=10;
            $fontWidth=13;
        }else{
            $fontSize=8;
            $fontWidth=11;
        }
        return [$fontSize,$fontWidth];
    }

    /**
     * 裁剪微信二维码的留白
     * @param $original 微信二维码图片
     * @return resource
     */
    public function cut_jpg($original){
        list($width, $height) = getimagesize($original);
        $qrcImg = imagecreatefromstring(file_get_contents($original));
        $image_p = imagecreatetruecolor(290, 290);
        imagecopyresampled($image_p, $qrcImg, 0, 0, 30, 30, 320, 320, $width-20, $height-20);
        return $image_p;
    }
}
