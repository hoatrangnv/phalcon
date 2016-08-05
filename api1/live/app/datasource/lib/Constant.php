<?php
namespace ITECH\Datasource\Lib;

class Constant
{
    const CODE_SUCCESS = 200;
    const CODE_ERROR = 400;

    const AUTH_TOKEN_STATUS_REQUEST = 1;
    const AUTH_TOKEN_STATUS_LOGINED = 2;

    const ADMIN_TYPE_ROOT = 'ROOT';
    const ADMIN_TYPE_ADMIN = 'ADMIN';
    const ADMIN_TYPE_EDITOR = 'EDITOR';
    const ADMIN_TYPE_AUTHOR = 'AUTHOR';
    const ADMIN_TYPE_INACTIVED = 'INACTIVED'; 

    const ADMIN_IS_LEADER = 1;
    const ADMIN_IS_MEMBER = 0;

    const ARTICLE_STATUS_ACTIVED = 'Y';
    const ARTICLE_STATUS_INACTIVED = 'N';
    const ARTICLE_STATUS_DELETED = 'D';
    const ARTICLE_STATUS_ARCHIVED = 'A';
    
    const PRODUCT_STATUS_ACTIVED = 'Y';
    const PRODUCT_STATUS_INACTIVED = 'N';
    const PRODUCT_STATUS_ARCHIVED = 'A';

    const STATUS_ACTIVED = 'Y';
    const STATUS_INACTIVED = 'N';
    const STATUS_ARCHIVED = 'A';
    const STATUS_DELETED = 'D';
    
    const CATEGORY_STATUS_ACTIVED = 'Y';
    const CATEGORY_STATUS_INACTIVED = 'N';
    const CATEGORY_STATUS_ARCHIVED = 'A';
    const CATEGORY_STATUS_DELETED ='D';  
    
    const ORDER_STATUS_NEW = 'N';
    const ORDER_STATUS_CONFIRMED = 'C';
    const ORDER_STATUS_PROCESSED = 'P';
    const ORDER_STATUS_FINISHED ='F';
    
    const PAYMENT_STATUS_PAID = 1;
    const PAYMENT_STATUS_UNPAID = 0;
    
    
    const GENDER_MALE = 'Y';
    const GENDER_FEMALE = 'N';
    const GENDER_UNDEFINED = 'U';
    
    const MODULE_PRODUCTS = 'products';
    const MODULE_ARTICLES = 'articles';
    const MODULE_PAGES = 'pages';
    const MODULE_FILES = 'files';
    
    const ARTICLES_DEFAULT = 'D';
    const ARTICLES_HOT = 'H';
    const ARTICLES_FOCUS = 'F';
    const ARTICLES_NEW = 'N';
    const ARTICLES_SELL = 'S';
    
    const USER_STATUS_ACTIVED = 'Y';
    const USER_STATUS_INACTIVED = 'N';
    const USER_STATUS_DELETED = 'D';
    
    const COMMENT_STATUS_DELETED = 0;
    const COMMENT_STATUS_ACTIVED = 1;
    const COMMENT_STATUS_INACTIVED = 2;
       
    const SEO_WEB_SITE = 'final.dev';  
    const SEO_WEB_SITE_DESCRIPTION = 'final nhung tin';
    const SEO_WEB_SITE_CATEGORY_TITLE = 'final nhung tin';
    const SEO_WEB_SITE_TAG = 'final.dev';
    const SEO_WEB_SITE_TAG_WEBSITE = 'final nhung tin';
    const MEMBER = 'Thành viên của final.dev';
    
    const COUNT = 0;
    const HITS = 0;
    const CATEGORY_DEFAULT = 0;
    const PAGINATION_15 = 15;
    const PAGINATION_20 = 20;
    const PAGINATION_25 = 25;
    const PAGINATION_30 = 30;
    const PAGINATION_35 = 35;
    const PAGINATION_40 = 40;
    const PAGINATION_45 = 45;
    const PAGINATION_50 = 50;
    const PAGINATION_55 = 55;
    const PAGINATION_60 = 60;
    const PAGINATION_65 = 65;
    const PAGINATION_70 = 70;
    const PAGINATION_75 = 75;
    const PAGINATION_80 = 80;
    const PAGINATION_85 = 85;
    const PAGINATION_90 = 90;
    const PAGINATION_95 = 95;
    const PAGINATION_100 = 100;
    
    /**
     * @author Cuong.Bui
     */
    public static function adminTypeSelect()
    {
        return array(
            self::ADMIN_TYPE_ROOT => 'ROOT',
            self::ADMIN_TYPE_ADMIN => 'ADMIN',
            self::ADMIN_TYPE_EDITOR => 'EDITOR',
            self::ADMIN_TYPE_AUTHOR => 'AUTHOR',
            self::ADMIN_TYPE_INACTIVED => 'INACTIVED'
        );
    }

    /**
     * @author Cuong.Bui
     */
    public static function adminTypeLabel()
    {
        return array(
            self::ADMIN_TYPE_ROOT => 'ROOT',
            self::ADMIN_TYPE_ADMIN => 'ADMIN',
            self::ADMIN_TYPE_EDITOR => 'EDITOR',
            self::ADMIN_TYPE_AUTHOR => 'AUTHOR',
            self::ADMIN_TYPE_INACTIVED => 'INACTIVED',     
        );
    }

    /**
     * @author Vu.Tran
     */
    public static function adminIsLeaderLabel()
    {
        return array(
            self::ADMIN_IS_MEMBER => 'M',
            self::ADMIN_IS_LEADER => 'L'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function moduleTypeLabel()
    {
        return array(
            self::MODULE_ARTICLES => 'Bài viết', 
            self::MODULE_PRODUCTS => 'Sản phẩm',
            self::MODULE_FILES => 'File',
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function paymentStatusLabel()
    {
        return array(
            1 => 'Đã thanh toán', 
            2 => 'Chưa thanh toán',
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function commentStatusLabel()
    {
        return array(
            self::COMMENT_STATUS_ACTIVED  => 'Duyệt', 
            self::COMMENT_STATUS_DELETED => 'Xóa',
            self::COMMENT_STATUS_INACTIVED => 'Ẩn',
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function orderStatusLabel()
    {
        return array(
            self::ORDER_STATUS_NEW => 'Mới', 
            self::ORDER_STATUS_CONFIRMED => 'Đã xác nhận',
            self::ORDER_STATUS_PROCESSED => 'Đã xử lý', 
            self::ORDER_STATUS_FINISHED => 'Hoàn thành'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function statusSelectOrder()
    {
        return array(
            self::ORDER_STATUS_NEW => 'Mới', 
            self::ORDER_STATUS_CONFIRMED => 'Đã xác nhận',
            self::ORDER_STATUS_PROCESSED => 'Đã xử lý', 
            self::ORDER_STATUS_FINISHED => 'Hoàn thành'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function targetSelect()
    {
        return array(
            1 => 'Open in new window', 
            2 => 'Window',
        );
    }
    /**
     * @author Vu.Tran
     */
    public static function provinceSelect()
    {
        return array(
            1 => 'TP.Hồ Chí Minh',
            2 => 'Hà Nội',
            3 => 'Bình Dương',
            4 => 'Đồng Nai',
            5 => 'Đà Nẵng',
            6 => 'Thanh Hóa',
            7 => 'Nghệ An',
            8 => 'Bà Rịa-Vũng Tàu',
            9 => 'Hải Phòng',
            10 => 'Hải Dương',
            11 => 'Nam Định',
            12 => 'Khánh Hòa',
            13 => 'Quảng Nam',
            14 => 'Cần Thơ',
            15 => 'An Giang',
            16 => 'Bạc Liêu',
            17 => 'Bắc Cạn',
            18 => 'Bắc Giang',
            19 => 'Bắc Ninh',
            20 => 'Bến Tre',
            21 => 'Bình Định',
            22 => 'Bình Phước',
            23 => 'Bình Thuận',
            24 => 'Cao Bằng',
            25 => 'Cà Mau',
            26 => 'Đắk Lắk',
            27 => 'Đắk Nông',
            28 => 'Điện Biên',
            29 => 'Đồng Tháp',
            30 => 'Gia Lai',
            31 => 'Hà Giang',
            32 => 'Hà Nam',
            33 => 'Hà Tây',
            34 => 'Hà Tĩnh',
            35 => 'Hậu Giang',
            36 => 'Hòa Bình',
            37 => 'Hưng Yên',
            38 => 'Kiên Giang',
            39 => 'Kon Tum',
            40 => 'Lai Châu',
            41 => 'Lạng Sơn',
            42 => 'Lào Cai',
            43 => 'Lâm Đồng',
            44 => 'Long An',
            45 => 'Ninh Bình',
            46 => 'Ninh Thuận',
            47 => 'Phú Thọ',
            48 => 'Phú Yên',
            49 => 'Quảng Bình',
            50 => 'Quảng Ngãi',
            51 => 'Quảng Ninh',
            52 => 'Quảng Trị',
            53 => 'Sóc Trăng',
            54 => 'Sơn La',
            55 => 'Tây Ninh',
            56 => 'Thái Bình',
            57 => 'Thái Nguyên',
            58 => 'Thừa Thiên-Huế',
            59 => 'Tiền Giang',
            60 => 'Trà Vinh',
            61 => 'Tuyên Quang',
            62 => 'Vĩnh Long',
            63 => 'Vĩnh Phúc',
            64 => 'Yên Bái',
            65 => 'Toàn quốc',
            66 => 'Nước ngoài'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function elementFormSelect()
    {
        return array(
            1 => 'text',
            2 => 'checkbox',
            3 => 'textarea',
            4 => 'select',
            5 => 'time',
            6 => 'color'
        );
    }

    /**
     * @author Vu.Tran
     */
    public static function contactTypeSelect()
    {
        return array(
            0 => 'Mọi hình thức',
            1 => 'Trực tiếp',
            2 => 'Qua email',
            3 => 'Qua điện thoại',
            4 => 'Qua bưu điện'
        );
    }

    /**
     * @author Vu.Tran
     */
    public static function genderSelect()
    {
        return array(
            self::GENDER_MALE => 'Nam',
            self::GENDER_FEMALE => 'Nữ',
            self::GENDER_UNDEFINED => 'Không xác định'
        );
    }

    /**
     * @author Vu.Tran
     */
    public static function statusSelect()
    {
        return array(
            self::STATUS_ACTIVED => 'Kích hoạt',
            self::STATUS_INACTIVED => 'Không kích hoạt',
            self::STATUS_ARCHIVED => 'Lưu trữ'
        );
    }
    
    public static function statusArticleSelect()
    {
        return array(
            self::STATUS_ACTIVED => 'Kích hoạt',
            self::STATUS_INACTIVED => 'Không kích hoạt',
            self::STATUS_ARCHIVED => 'Lưu trữ'
        );
    }
    
    public static function statusProductSelect()
    {
        return array(
            self::STATUS_ACTIVED => 'Kích hoạt',
            self::STATUS_INACTIVED => 'Không kích hoạt',
            self::STATUS_ARCHIVED => 'Lưu trữ'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function articleTypeSelect()
    {
        return array(
            self::ARTICLES_DEFAULT => 'Bình thường',
            self::ARTICLES_NEW => 'Nổi bật',
            self::ARTICLES_HOT => 'Hot',
            self::ARTICLES_FOCUS => 'Tiêu điểm'
        );
    } 
    
    /**
     * @author Vu.Tran
     */
    public static function productTypeSelect()
    {
        return array(
            self::ARTICLES_DEFAULT => 'Bình thường',
            self::ARTICLES_NEW => 'Mới',
            self::ARTICLES_HOT => 'Hot',
            self::ARTICLES_FOCUS => 'Tiêu điểm',
            self::ARTICLES_SELL => 'Giảm giá'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function pageTypeSelect()
    {
        return array(
            self::ARTICLES_DEFAULT => 'Bình thường',
            self::ARTICLES_NEW => 'Mới',
            self::ARTICLES_HOT => 'Hot'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function themeSelect()
    {
        return array(
            'default' => 'Mặc định',
            'theme/furniture/default' => 'Nội thất',
            'theme/law/default' => 'Văn bản pháp luật',
            'theme/blog/default' => 'Blog MBN'
            
        );
    }

    /**
     * @author Vu.Tran
     */
    public static function userDayRangeSelect()
    {
        return array(
            0 => 'Ngày',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12',
            13 => '13',
            14 => '14',
            15 => '15',
            16 => '16',
            17 => '17',
            18 => '18',
            19 => '19',
            20 => '20',
            21 => '21',
            22 => '22',
            23 => '23',
            24 => '24',
            25 => '25',
            26 => '26',
            27 => '27',
            28 => '28',
            29 => '29',
            30 => '30',
            31 => '31'
        );
    }

    /**
    * @author Vu.Tran
    */
    public static function userMonthRangeSelect()
    {
        return array(
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12'
        );
    }
    
    /**
    * @author Vu.Tran
    */
    public static function hourSelect()
    {
        return array(
            '' => 'Giờ',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12',
            13 => '13',
            14 => '14',
            15 => '15',
            16 => '16',
            17 => '17',
            18 => '18',
            19 => '19',
            20 => '20',
            21 => '21',
            22 => '22',
            23 => '23',
            24 => '24'   
        );
    }
    
    /**
    * @author Vu.Tran
    */
    public static function minuteSelect()
    {
        return array(
            '' => 'Phút',
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12',
            13 => '13',
            14 => '14',
            15 => '15',
            16 => '16',
            17 => '17',
            18 => '18',
            19 => '19',
            20 => '20',
            21 => '21',
            22 => '22',
            23 => '23',
            24 => '24',
            25 => '25',
            26 => '26',
            27 => '27',
            28 => '28',
            29 => '29',
            30 => '30',
            31 => '31',
            32 => '32',
            33 => '33',
            34 => '34',
            35 => '35',
            36 => '36',
            37 => '37',
            38 => '38',
            39 => '39',
            40 => '40',
            41 => '41',
            42 => '42',
            43 => '43',
            44 => '44',
            45 => '45',
            46 => '46',
            47 => '47',
            48 => '48',
            49 => '49',
            50 => '50',
            51 => '51',
            52 => '52',
            53 => '53',
            54 => '54',
            55 => '55',
            56 => '56',
            57 => '57',
            58 => '58',
            59 => '59',
            60 => '60'
        );
    }
    
    /**
     * @author Vu.Tran
     */
    public static function userYearRangeSelect()
    {
        $years = array(0 => 'Năm');
        $start = (int)date('Y');
        $end = (int)date('Y', strtotime('-50 years'));

        for ($year = $start; $year >= $end; $year--) {
            if ($year > 0) {
                $years[$year] = $year;
            }
        }

        return $years;
    }
}