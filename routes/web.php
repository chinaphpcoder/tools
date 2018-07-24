<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

//解决反向代理问题
//$proxy_url = env('PROXY_URL');
//if (!empty($proxy_url)) {
//    URL::forceRootUrl($proxy_url);
//}
//$proxy_scheme = env('PROXY_SCHEMA');
//if (!empty($proxy_scheme)) {
//    URL::forceScheme($proxy_scheme);
//}

//首页
Route::get('/', 'HomeController@index')->middleware('auth')->name('home');


//上传图片
Route::post('/uploadImage', 'PublicController@uploadImage')->middleware('auth')->name('upload_image');
Route::post('/ckeditorImage', 'PublicController@ckeditorImage')->middleware('auth')->name('ckeditor_image');

//菜单管理
Route::get('/menu/index', 'MenuController@lists')->middleware('auth')->name('menu_list');
Route::get('/menu/add', 'MenuController@add')->middleware('auth')->name('menu_add');
Route::get('/menu/edit', 'MenuController@edit')->middleware('auth')->name('menu_edit');
Route::post('/menu/update', 'MenuController@update')->middleware('auth')->name('menu_update');

//法律法规
Route::get('/legal/index', 'LegalController@index')->middleware('auth')->name('legal_list');
Route::get('/legal/add', 'LegalController@add')->middleware('auth')->name('legal_add');
Route::get('/legal/edit', 'LegalController@edit')->middleware('auth')->name('legal_edit');
Route::post('/legal/update', 'LegalController@update')->middleware('auth')->name('legal_update');
Route::get('/legal/delete', 'LegalController@delete')->middleware('auth')->name('legal_delete');

//合作机构
Route::get('/friend/index', 'GoodFriendController@index')->middleware('auth')->name('friend_list');
Route::get('/friend/add', 'GoodFriendController@add')->middleware('auth')->name('friend_add');
Route::get('/friend/edit', 'GoodFriendController@edit')->middleware('auth')->name('friend_edit');
Route::post('/friend/update', 'GoodFriendController@update')->middleware('auth')->name('friend_update');
Route::get('/friend/delete', 'GoodFriendController@delete')->middleware('auth')->name('friend_delete');

//友情链接
Route::get('/link/index', 'LinkController@index')->middleware('auth')->name('link_list');
Route::get('/link/add', 'LinkController@add')->middleware('auth')->name('link_add');
Route::get('/link/edit', 'LinkController@edit')->middleware('auth')->name('link_edit');
Route::post('/link/update', 'LinkController@update')->middleware('auth')->name('link_update');
Route::get('/link/delete', 'LinkController@delete')->middleware('auth')->name('link_delete');

//轮播图
Route::get('/carousel/index', 'CarouselController@index')->middleware('auth')->name('carousel_list');
Route::get('/carousel/add', 'CarouselController@add')->middleware('auth')->name('carousel_add');
Route::get('/carousel/edit', 'CarouselController@edit')->middleware('auth')->name('carousel_edit');
Route::post('/carousel/update', 'CarouselController@update')->middleware('auth')->name('carousel_update');
Route::get('/carousel/delete', 'CarouselController@delete')->middleware('auth')->name('carousel_delete');
Route::get('/carousel/status', 'CarouselController@status')->middleware('auth')->name('carousel_status');
Route::post('/carousel/order', 'CarouselController@order')->middleware('auth')->name('carousel_order');

//H5项目管理---轮播图
Route::get('banner/index', 'BannerController@index')->middleware('auth')->name('banner_index');
Route::get('banner/add', 'BannerController@create')->middleware('auth')->name('banner_add');
Route::post('banner/update', 'BannerController@update')->middleware('auth')->name('banner_update');
Route::get('banner/delete', 'BannerController@destroy')->middleware('auth')->name('banner_delete');
Route::get('banner/edit', 'BannerController@edit')->middleware('auth')->name('banner_edit');
Route::get('banner/status', 'BannerController@status')->middleware('auth')->name('banner_status');
Route::post('banner/order', 'BannerController@order')->middleware('auth')->name('banner_order');
//H5公告管理
Route::get('article/index', 'ArticleController@index')->middleware('auth')->name('article_index');
Route::get('article/add', 'ArticleController@add')->middleware('auth')->name('article_add');
Route::get('article/delete', 'ArticleController@delete')->middleware('auth')->name('article_delete');
Route::post('article/update', 'ArticleController@update')->middleware('auth')->name('article_update');
Route::get('article/edit', 'ArticleController@edit')->middleware('auth')->name('article_edit');
Route::get('article/delete', 'ArticleController@delete')->middleware('auth')->name('article_delete');
Route::get('article/status', 'ArticleController@status')->middleware('auth')->name('article_status');

//小沙类型管理
Route::get('/type/index', 'TypeController@index')->middleware('auth')->name('type_list');
Route::get('/type/add', 'TypeController@add')->middleware('auth')->name('type_add');
Route::get('/type/edit', 'TypeController@edit')->middleware('auth')->name('type_edit');
Route::post('/type/update', 'TypeController@update')->middleware('auth')->name('type_update');
Route::get('/type/delete', 'TypeController@delete')->middleware('auth')->name('type_delete');

//小沙学院
Route::get('/college/index', 'CollegeController@index')->middleware('auth')->name('college_list');
Route::get('/college/add', 'CollegeController@add')->middleware('auth')->name('college_add');
Route::get('/college/edit', 'CollegeController@edit')->middleware('auth')->name('college_edit');
Route::post('/college/update', 'CollegeController@update')->middleware('auth')->name('college_update');
Route::get('/college/delete', 'CollegeController@delete')->middleware('auth')->name('college_delete');
Route::get('/college/status', 'CollegeController@status')->middleware('auth')->name('college_status');

//媒体报道
Route::get('/news/index', 'NewsController@index')->middleware('auth')->name('news_list');
Route::get('/news/add', 'NewsController@add')->middleware('auth')->name('news_add');
Route::get('/news/edit', 'NewsController@edit')->middleware('auth')->name('news_edit');
Route::post('/news/update', 'NewsController@update')->middleware('auth')->name('news_update');
Route::get('/news/delete', 'NewsController@delete')->middleware('auth')->name('news_delete');
Route::get('/news/status', 'NewsController@status')->middleware('auth')->name('news_status');

//后台用户管理
Route::get('/user/index', 'UserController@index')->middleware('auth')->name('user_list');
Route::get('/user/add', 'UserController@add')->middleware('auth')->name('user_add');
Route::get('/user/edit', 'UserController@edit')->middleware('auth')->name('user_edit');
Route::post('/user/update', 'UserController@update')->middleware('auth')->name('user_update');
Route::get('/user/delete', 'UserController@delete')->middleware('auth')->name('user_delete');

Route::get('/admin/activity_menu', 'AdminController@activity_menu')->middleware('auth')->name('admin.activity_menu');
Route::get('/admin/activity_add', 'AdminController@activity_add')->middleware('auth')->name('admin.activity_add');
Route::post('/admin/activity_update', 'AdminController@activity_update')->middleware('auth')->name('admin.activity_update');
Route::get('/admin/activity_delete', 'AdminController@activity_delete')->middleware('auth')->name('admin.activity_delete');

//小沙故事
Route::get('/story/index', 'StoryController@index')->middleware('auth')->name('story_index');
Route::get('/story/add', 'StoryController@add')->middleware('auth')->name('story_add');
Route::get('/story/edit', 'StoryController@edit')->middleware('auth')->name('story_edit');
Route::post('/story/update', 'StoryController@update')->middleware('auth')->name('story_update');
Route::get('/story/delete', 'StoryController@delete')->middleware('auth')->name('story_delete');
Route::get('/story/status', 'StoryController@status')->middleware('auth')->name('story_status');
Route::get('/story/message', 'StoryController@message')->middleware('auth')->name('story_message');
Route::get('/story/delmessage', 'StoryController@deleteMessage')->middleware('auth')->name('story_delMessage');

//转盘活动管理
Route::get('/zhuanpan/user_list', 'ActivityController@index')->middleware('auth')->name('user_list');
Route::get('/zhuanpan/create', 'ActivityController@create')->middleware('auth')->name('user_create');
Route::post("/zhuanpan/store", 'ActivityController@store')->middleware('auth')->name('user_store');
Route::get('/zhuanpan/download', 'ActivityController@download')->middleware('auth')->name('user_download');
Route::get('/zhuanpan/delete', 'ActivityController@delete')->middleware('auth')->name('user_delete');

Route::any('/zhuanpan/user_list_all', 'ActivityController@userActivityList')->middleware('auth')->name('user_list_all');
Route::get('/zhuanpan/export', 'ActivityController@export')->middleware('auth')->name('user_export');
//PC轮播图
Route::get('pc/banner/index', 'PCBannerController@index')->middleware('auth')->name('pcbanner_index');
Route::get('pc/banner/add', 'PCBannerController@create')->middleware('auth')->name('pcbanner_add');
Route::post('pc/banner/update', 'PCBannerController@update')->middleware('auth')->name('pcbanner_update');
Route::get('pc/banner/delete', 'PCBannerController@destroy')->middleware('auth')->name('pcbanner_delete');
Route::get('pc/banner/edit', 'PCBannerController@edit')->middleware('auth')->name('pcbanner_edit');
Route::get('pc/banner/status', 'PCBannerController@status')->middleware('auth')->name('pcbanner_status');
Route::post('pc/banner/order', 'PCBannerController@order')->middleware('auth')->name('pcbanner_order');

// 世界杯活动管理
Route::post('world/createTeam', 'WorldcupController@createTeam')->middleware('auth')->name('createTeam');
Route::get('world/team', 'WorldcupController@footballTeam')->middleware('auth')->name('team_list');
Route::get('world/add', 'WorldcupController@add')->middleware('auth')->name('add_team');
Route::get('world/edit', 'WorldcupController@edit')->middleware('auth')->name('world_team_edit');
Route::get('world/delteam', 'WorldcupController@delteam')->middleware('auth')->name('world_team_delete');
Route::get('world/match', 'WorldcupController@match')->middleware('auth')->name('world_team_match');
Route::get('world/add_match', 'WorldcupController@add_match')->middleware('auth')->name('world_add_match');
Route::post('world/doadd_match', 'WorldcupController@doadd_match')->middleware('auth')->name('world_doadd_match');
Route::get('world/worldMatchEdit', 'WorldcupController@worldMatchEdit')->middleware('auth')->name('world_match_edit');
Route::post('world/worldDoeditMatch', 'WorldcupController@worldDoeditMatch')->middleware('auth')->name('world_doedit_match');
Route::get('world/worldMatchDelete', 'WorldcupController@worldMatchDelete')->middleware('auth')->name('world_match_delete');
Route::get('world/worldGroup', 'WorldcupController@worldGroup')->middleware('auth')->name('world_user_group');
Route::get('world/export', 'WorldcupController@export')->middleware('auth')->name('group_export');
Route::get('world/finals', 'WorldcupController@finals')->middleware('auth')->name('finals');
Route::get('world/finals_export', 'WorldcupController@finals_export')->middleware('auth')->name('finals_export');





//广告位管理
Route::get('/advertisement/fixed/index', 'AdvertisementController@fixed')->middleware('auth')->name('fixed_index');
Route::get('/advertisement/fixed/add', 'AdvertisementController@add')->middleware('auth')->name('fixed_add');
Route::post('/advertisement/fixed/create', 'AdvertisementController@create')->middleware('auth')->name('fixed_create');
Route::post('/advertisement/fixed/update', 'AdvertisementController@update')->middleware('auth')->name('fixed_update');
Route::get('/advertisement/fixed/edit', 'AdvertisementController@edit')->middleware('auth')->name('fixed_edit');
Route::get('/advertisement/fixed/status', 'AdvertisementController@status')->middleware('auth')->name('fixed_status');
Route::get('/advertisement/fixed/delete', 'AdvertisementController@destroy')->middleware('auth')->name('fixed_delete');

//APP公告
Route::get('notice/app/index', 'NoticeController@clientIndex')->middleware('auth')->name('app_notice');
Route::get('notice/app/add', 'NoticeController@clientAdd')->middleware('auth')->name('app_add');
Route::post('notice/app/update', 'NoticeController@clientUpdate')->middleware('auth')->name('app_update');
Route::get('notice/app/status', 'NoticeController@clientStatus')->middleware('auth')->name('app_status');
Route::get('notice/app/delete', 'NoticeController@clientDelete')->middleware('auth')->name('app_delete');
Route::get('notice/app/edit', 'NoticeController@clientEdit')->middleware('auth')->name('app_edit');

//端午活动管理
Route::get('/activity/dragonboat/setting', 'DragonBoatController@setting')->middleware('auth')->name('dragonboat_setting');
Route::get('/activity/dragonboat/prize_list', 'DragonBoatController@prize_list')->middleware('auth')->name('dragonboat_prize_list');
Route::get('/activity/dragonboat/probability_setting', 'DragonBoatController@probability_setting')->middleware('auth')->name('probability_setting');
Route::post('/activity/dragonboat/probability_update', 'DragonBoatController@probability_update')->middleware('auth')->name('probability_update');

//活动管理

Route::get('/activity/prize/manage/{identification?}', 'PrizeController@manage')->middleware('auth')->name('activity.prize.manage');
Route::get('/activity/prize/prize_list/{identification?}', 'PrizeController@prize_list')->middleware('auth')->name('activity.prize.prize_list');
Route::get('/activity/prize/probability', 'PrizeController@probability_edit')->middleware('auth')->name('activity.prize.probability.edit');
Route::put('/activity/prize/probability', 'PrizeController@probability_update')->middleware('auth')->name('activity.prize.probability.update');
Route::get('/activity/prize/details', 'PrizeController@details')->middleware('auth')->name('activity.prize.details');

Route::get('/activity/prize/winning_record/{identification?}', 'PrizeController@winning_record')->middleware('auth')->name('activity.prize.winning_record');
Route::get('/activity/prize/winning_record_export/{identification?}', 'PrizeController@winning_record_export')->middleware('auth')->name('activity.prize.winning_record_export');

Route::get('/activity/prize/list/{identification?}', 'PrizeController@lists')->middleware('auth')->name('activity_prize_lists');
Route::get('/activity/prize/export/{identification?}', 'PrizeController@export')->middleware('auth')->name('activity_prize_export');


//微信管理
Route::get('/weixin/auto_reply', 'WeixinController@autoReply')->middleware('auth')->name('weixin.auto_reply');
Route::get('/weixin/reply_list', 'WeixinController@replyList')->middleware('auth')->name('weixin.reply_list');
Route::get('/weixin/reply_setting', 'WeixinController@replySetting')->middleware('auth')->name('weixin.reply_setting');
Route::post('/weixin/reply_update', 'WeixinController@replyUpdate')->middleware('auth')->name('weixin.reply_update');

Route::get('/finance/account-record','FinanceController@accountRecord')->middleware('auth')->name('finance.account-record');
Route::get('/finance/get-account-record','FinanceController@getAccountRecord')->middleware('auth')->name('finance.get-account-record');
Route::post('/finance/add-account-record','FinanceController@addAccountRecord')->middleware('auth')->name('finance.add-account-record');
Route::get('/finance/account-record-details','FinanceController@accountRecordDetails')->middleware('auth')->name('finance.account-record-details');
Route::post('/finance/upload-basic-data','FinanceController@uploadBasicData')->middleware('auth')->name('finance.upload-basic-data');
Route::post('/finance/upload-actual-data','FinanceController@uploadActualData')->middleware('auth')->name('finance.upload-actual-data');

Route::get('/finance/show-error-data','FinanceController@showErrorData')->middleware('auth')->name('finance.show-error-data');
