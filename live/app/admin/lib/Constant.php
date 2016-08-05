<?php
namespace ITECH\Admin\Lib;

use ITECH\Datasource\Lib\Constant as GlobalConstant;

class Constant
{
    const SEARCH_ONLY_PACKAGE = 5;
    const TYPE_HOT_JOB = 1;
    const TYPE_FAST_JOB = 2;

    /**
     * @author Vu.Tran
     */
    public static function sideBar()
    {
        return array(
            'home' => array(
                'title' => 'Dashboard',
                'controller' => 'home',
                'action' => 'index',
                'icon_class' => 'clip-home-3',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_AUTHOR, GlobalConstant::ADMIN_TYPE_EDITOR)
            ),
            'category' => array(
                'title' => 'Quản lý danh mục',
                'controller' => 'category',
                'action' => 'index',
                'icon_class' => 'clip-list',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'category' => array(
                        'title' => 'Danh mục',
                        'controller' => 'category',
                        'action' => 'index',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'category_attribute_group' => array(
                        'title' => 'Nhóm sản phẩm',
                        'controller' => 'category',
                        'action' => 'attributeGroup',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'category_attribute' => array(
                        'title' => 'Thuộc tính nhóm',
                        'controller' => 'category',
                        'action' => 'attribute',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'category_tag' => array(
                        'title' => 'Tags',
                        'controller' => 'category',
                        'action' => 'tag',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )  
                )
            ),
            'article' => array(
                'title' => 'Quản lý bài viết',
                'controller' => 'article',
                'action' => 'index',
                'icon_class' => 'clip-new',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'article' => array(
                        'title' => 'Danh sách bài viết',
                        'controller' => 'article',
                        'action' => 'index',
                        'icon_class' => 'clip-book',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'article_add' => array(
                        'title' => 'Thêm bài viết',
                        'controller' => 'article',
                        'action' => 'add',
                        'icon_class' => 'clip-book',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'product' => array(
                'title' => 'Quản lý sản phẩm',
                'controller' => 'product',
                'action' => 'index',
                'icon_class' => 'clip-laptop',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN),
                'sub_menu' => array(
                    'product' => array(
                        'title' => 'Danh sách sản phẩm',
                        'controller' => 'product',
                        'action' => 'index',
                        'icon_class' => 'clip-laptop',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'product_add' => array(
                        'title' => 'Thêm sản phẩm',
                        'controller' => 'product',
                        'action' => 'add',
                        'icon_class' => 'clip-laptop',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'comment' => array(
                'title' => 'Quản lý bình luận',
                'controller' => 'comment',
                'action' => 'index',
                'icon_class' => 'clip-paperclip',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'comment' => array(
                        'title' => 'Danh sách bình luận',
                        'controller' => 'page',
                        'action' => 'index',
                        'icon_class' => 'clip-paperclip',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'page' => array(
                'title' => 'Quản lý trang tĩnh',
                'controller' => 'page',
                'action' => 'index',
                'icon_class' => 'clip-paperclip',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'page' => array(
                        'title' => 'Danh sách trang tĩnh',
                        'controller' => 'page',
                        'action' => 'index',
                        'icon_class' => 'clip-paperclip',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'page_add' => array(
                        'title' => 'Thêm trang tĩnh',
                        'controller' => 'page',
                        'action' => 'add',
                        'icon_class' => 'clip-paperclip',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'link' => array(
                'title' => 'Quản lý liên kết',
                'controller' => 'link',
                'action' => 'index',
                'icon_class' => 'clip-link',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'link' => array(
                        'title' => 'Nhóm liên kết',
                        'controller' => 'link',
                        'action' => 'index',
                        'icon_class' => 'clip-link',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'link_add' => array(
                        'title' => 'Thêm liên kết',
                        'controller' => 'link',
                        'action' => 'add',
                        'icon_class' => 'clip-link',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'ad' => array(
                'title' => 'Quản lý quảng cáo',
                'controller' => 'ad',
                'action' => 'index',
                'icon_class' => 'clip-windows8',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'ad_position' => array(
                        'title' => 'Vị trí',
                        'controller' => 'ad',
                        'action' => 'position',
                        'icon_class' => 'clip-windows8',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'ad_position_add' => array(
                        'title' => 'Thêm vị trí',
                        'controller' => 'ad',
                        'action' => 'positionAdd',
                        'icon_class' => 'clip-windows8',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'ad' => array(
                        'title' => 'Quảng cáo',
                        'controller' => 'ad',
                        'action' => 'index',
                        'icon_class' => 'clip-windows8',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'ad_add' => array(
                        'title' => 'Thêm quảng cáo',
                        'controller' => 'ad',
                        'action' => 'add',
                        'icon_class' => 'clip-link',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'order' => array(
                'title' => 'Quản lý đơn hàng',
                'controller' => 'order',
                'action' => 'index',
                'icon_class' => 'clip-pencil-2',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'order' => array(
                        'title' => 'Danh sách đơn hàng',
                        'controller' => 'order',
                        'action' => 'index',
                        'icon_class' => 'clip-pencil-2',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    )
                )
            ),
            'file' => array(
                'title' => 'Quản lý file',
                'controller' => 'file',
                'action' => 'index',
                'icon_class' => 'clip-pencil-2',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'file_category' => array(
                        'title' => 'Danh sách thư mục',
                        'controller' => 'file',
                        'action' => 'category',
                        'icon_class' => 'clip-pencil-2',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'file' => array(
                        'title' => 'Danh sách file',
                        'controller' => 'file',
                        'action' => 'index',
                        'icon_class' => 'clip-pencil-2',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ) 
                )
            ),
            'analytic' => array(
                'title' => 'Thống kê',
                'controller' => 'analytic',
                'action' => 'index',
                'icon_class' => 'clip-atom-3',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR),
                'sub_menu' => array(
                    'analytic_article' => array(
                        'title' => 'Thống kê sản phẩm',
                        'controller' => 'analytic',
                        'action' => 'article',
                        'icon_class' => 'clip-pencil-2',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ),
                    'analytic_product' => array(
                        'title' => 'Thống kê bài viết',
                        'controller' => 'analytic',
                        'action' => 'product',
                        'icon_class' => 'clip-pencil-2',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR)
                    ) 
                )
            ),
            /*'accountant_queue_list' => array(
                'title' => 'Kế toán',
                'controller' => 'accountant',
                'action' => 'queueList',
                'icon_class' => 'clip-folder',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN),
                'sub_menu' => array(
                    'accountant_queue_list' => array(
                        'title' => 'Chờ duyệt',
                        'controller' => 'accountant',
                        'action' => 'queueList',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN)
                    ),
                    'accountant_queue_employer_history_list' => array(
                        'title' => 'Lịch sử duyệt tài khoản',
                        'controller' => 'accountant',
                        'action' => 'employerHistoryList',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN)
                    ),
                    'accountant_queue_job_history_list' => array(
                        'title' => 'Lịch sử duyệt tin',
                        'controller' => 'accountant',
                        'action' => 'jobHistoryList',
                        'icon_class' => 'clip-folder',
                        'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN)
                    )
                )
            ),
            'seo' => array(
                'title' => 'SEO',
                'controller' => 'seo',
                'action' => 'index',
                'icon_class' => 'clip-folder',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN)
            ),*/
            'user_list' => array(
                'title' => 'Thành viên',
                'controller' => 'user',
                'action' => 'index',
                'icon_class' => 'clip-user-2',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN)
            ),
            'profile' => array(
                'title' => 'Tài khoản',
                'controller' => GlobalConstant::ADMIN_TYPE_ADMIN,
                'action' => 'profile',
                'icon_class' => 'clip-user-2',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR, GlobalConstant::ADMIN_TYPE_AUTHOR) 
            ),
            'config' => array(
                'title' => 'Cài đặt',
                'controller' => GlobalConstant::ADMIN_TYPE_ADMIN,
                'action' => 'config',
                'icon_class' => 'clip-settings',
                'role' => array(GlobalConstant::ADMIN_TYPE_ROOT, GlobalConstant::ADMIN_TYPE_ADMIN, GlobalConstant::ADMIN_TYPE_EDITOR, GlobalConstant::ADMIN_TYPE_AUTHOR)
            )
        );
    }

    /**
     * @author Cuong.Bui
     */
    public static function accountantPackageSelect()
    {
        return array(
            1 => array(
                'title' => 'Xác thực tài khoản nhà tuyển dụng',
                'items' => array(
                    1 => array(
                        'title' => 'Xác thực tài khoản + Tìm hồ sơ',
                        'week' => 8,
                        'unit_price' => 1700000,
                        'promotion' => 1500000,
                        'note' => '- Tặng 8 tuần cover.'
                    ),
                    2 => array(
                        'title' => 'Xác thực tài khoản + Tìm hồ sơ',
                        'week' => 13,
                        'unit_price' => 2762500,
                        'promotion' => 2200000,
                        'note' => '- Tặng 13 tuần cover.'
                    ),
                    3 => array(
                        'title' => 'Xác thực tài khoản + Tìm hồ sơ',
                        'week' => 26,
                        'unit_price' => 5525000,
                        'promotion' => 4200000,
                        'note' => "- Tặng 26 tuần cover.\n- Tặng xác thực tài khoản 4 tuần (hoặc thẻ cào 200,000)."
                    ),
                    4 => array(
                        'title' => 'Xác thực tài khoản + Tìm hồ sơ',
                        'week' => 52,
                        'unit_price' => 11050000,
                        'promotion' => 7500000,
                        'note' => "- Tặng 52 tuần cover.\n- Tặng xác thực tài khoản 8 tuần (hoặc thẻ cào 500,000)."
                    ),
                    self::SEARCH_ONLY_PACKAGE => array(
                        'title' => 'Tìm hồ sơ ứng viên',
                        'week' => 4,
                        'unit_price' => 850000,
                        'promotion' => 850000,
                        'note' => '- Không được cập nhật Logo nhà tuyển dụng.'
                    )
                )
            ),
            2 => array(
                'title' => 'Tin tiêu điểm',
                'items' => array(
                    1 => array(
                        'title' => 'Tin tiêu điểm + Tìm hồ sơ',
                        'week' => 4,
                        'unit_price' => 1500000,
                        'promotion' => 1500000,
                        'note' => "- Xem HS không giới hạn.\n- Tặng 4 tuần xác thực tài khoản trị giá 600,000.\n- Tặng 1 tháng cover.\n- Tặng thẻ cào điện thoại 200,000."
                    ),
                    2 => array(
                        'title' => 'Tin tiêu điểm HOT + Tìm hồ sơ',
                        'week' => 4,
                        'unit_price' => 2500000,
                        'promotion' => 2500000,
                        'note' => "- Dòng tin màu đỏ nổi bật + HOT.\n- Xem HS không giới hạn.\n- Tặng 4 tuần xác thực tài khoản trị giá 600,000.\n- Tặng 1 tháng cover.\n- Tặng thẻ cào điện thoại 200,000."
                    )
                )
            ),
            3 => array(
                'title' => 'Tin tuyển dụng nhanh',
                'items' => array(
                    1 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 1,
                        'unit_price' => 1200000,
                        'promotion' => 1200000,
                        'note' => '- 1 tuần xác thực tài khoản.'
                    ),
                    2 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 2,
                        'unit_price' => 2400000,
                        'promotion' => 2200000,
                        'note' => '- 2 tuần xác thực tài khoản + Tặng thẻ cào 100,000.'
                    ),
                    3 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 3,
                        'unit_price' => 3600000,
                        'promotion' => 3100000,
                        'note' => '- 3 tuần xác thực tài khoản + Tặng thẻ cào 200,000.'
                    ),
                    4 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 4,
                        'unit_price' => 4800000,
                        'promotion' => 4000000,
                        'note' => '- 4 tuần xác thực tài khoản + Tặng thẻ cào 300,000.'
                    ),
                    5 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 8,
                        'unit_price' => 9600000,
                        'promotion' => 7700000,
                        'note' => '- 8 tuần xác thực tài khoản + Tặng thẻ cào 500,000.'
                    ),
                    6 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 13,
                        'unit_price' => 15600000,
                        'promotion' => 12100000,
                        'note' => '- 13 tuần xác thực tài khoản + Tặng thẻ cào 800,000.'
                    ),
                    7 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 26,
                        'unit_price' => 31200000,
                        'promotion' => 23700000,
                        'note' => '- 26 tuần xác thực tài khoản + Tặng thẻ cào 1,000,000.'
                    ),
                    8 => array(
                        'title' => 'Tin tuyển dụng nhanh + Tìm hồ sơ',
                        'week' => 52,
                        'unit_price' => 62400000,
                        'promotion' => 45400000,
                        'note' => '- 52 tuần xác thực tài khoản + Tặng thẻ cào 1,200,000.'
                    )
                )
            )
        );
    }

    /**
     * @author Cuong.Bui
     */
    public static function accountantBenefitSelect()
    {
        return array(
            1 => array(
                1 => array(
                    'title' => 'Quyền lợi của nhà tuyển dụng khi tham gia "Xác thực tài khoản nhà tuyển dụng"',
                    'benefit' => '
                        <ul>
                            <li>Thông tin tuyển dụng được ưu tiên hiển thị hàng đầu trong từng lĩnh vực, ngành nghề.</li>
                            <li>Tìm hồ sơ, lọc hồ sơ ứng viên trong hàng chục nghìn hồ sơ đăng ký tìm việc (Không giới hạn).</li>
                            <li>Hiển thị Logo và website doanh nghiệp trong tin tuyển dụng tại www.TimViecNhanh.com.</li>
                            <li>Nâng cao thương hiệu và uy tín nhà tuyển dụng để thu hút nhân tài.</li>
                            <li>Tuyển dụng nhanh nhất vì có hàng ngàn website việc làm khác đăng lại tin tuyển dụng.</li>
                        </ul>
                    ',
                    'more_info' => '
                        <ul>
                            <li>Đối với các tài khoản đăng ký tham gia lần đầu quý khách vui lòng bổ sung thông tin con dấu vào phiếu xác thực này đối với khách hàng gia hạn quý khách chỉ cần Fax hoặc email CSKH TimViecNhanh.com.</li>
                            <li>Nhà tuyển dụng đảm bảo rằng những vị trí tuyển dụng hoàn toàn nghiêm túc, chính xác và tuân thủ theo điều khoản sử dụng tại website www.TimViecNhanh.com lúc đăng ký tài khoản.</li>
                            <li>Nếu Nhà tuyển dụng vi phạm điều khoản sử dụng thì tài khoản xác thực sẽ không còn hiệu lực. Phiếu đăng ký này thay cho hợp đồng thỏa thuận và có giá trị pháp lý như nhau.</li>
                        </ul>
                    '
                ),
                2 => array(
                    'title' => 'Quyền lợi của nhà tuyển dụng khi tham gia "Tìm hồ sơ ứng viên"',
                    'benefit' => '
                        <ul>
                            <li>Tìm hồ sơ, lọc hồ sơ ứng viên trong hàng chục nghìn hồ sơ đăng ký tìm việc (không giới hạn).</li>
                        </ul>
                    ',
                    'more_info' => '
                        <ul>
                            <li>Chúng tôi đảm bảo rằng những vị trí tuyển dụng của chúng tôi hoàn toàn nghiêm túc và chính xác về thông tin tuyển dụng.</li>
                            <li>Các khuyến mãi đi kèm không quy đổi thành tiền mặt.</li>
                            <li>Mọi thông tin đăng tải từ website www.TuyendDungNhanh.com, www.TimViecNhanh.com không được sử dụng kinh doanh giới thiệu việc làm hoặc thu tiền ứng viên dưới bất cứ hình thức nào, nếu chúng tôi vi phạm nguyên tắc này thì tài khoản xác thực sẽ không còn hiệu lực.</li>
                            <li>Phiếu đăng ký này thay cho hợp đồng thỏa thuận và có giá trị pháp lý như nhau.</li>
                        </ul>
                    '
                )
            ),
            2 => array(),
            3 => array()
        );
    }
}