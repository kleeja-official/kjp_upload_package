<?php
// Kleeja Plugin
// KJPAY_UPLOAD_PACKAGE
// Version: 1.0
// Developer: Kleeja Team

// Prevent illegal run
if (! defined('IN_PLUGINS_SYSTEM'))
{
    exit();
}


// Plugin Basic Information
$kleeja_plugin['kjp_upload_package']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'KJPay Upload Package',
        'ar' => 'حزم الرفع للموقع'
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja Team',
    // This plugin version
    'plugin_version' => '1.0',
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' => 'give upload permission to users who have valid subscription only',
        'ar' => 'منح إذن تحميل للمستخدمين الذين لديهم اشتراك صالح فقط'
    ],
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '3.1.4',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '3.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['kjp_upload_package']['first_run']['ar'] = '

';
$kleeja_plugin['kjp_upload_package']['first_run']['en'] = '
باستخدام هذ الإضافة ، يمكنك منح إذن التحميل للمستخدمين الذين لديهم اشتراك صالح فقط <br>
إنه لا يعمل بدون اضافة (kleeja_payment)
';


// Plugin Installation function
$kleeja_plugin['kjp_upload_package']['install'] = function ($plg_id) {
    if (! defined('support_kjPay'))
    {
        // Don't install this plugin if kleeja_payment is not installed
        $ERR = 
        [
            'ar' => 'هذه عبارة عن ملحق ل `kleeja_payment` ، الرجاء تثبيته ثم تثبيت هذه الاضافة' ,
            'en' => 'this is a package of `kleeja_payment` plugin , Please install it then install this plugin'
        ];

        global $SQL , $dbprefix , $config;

        $SQL->query("DELETE FROM `{$dbprefix}plugins` WHERE `plg_id` = {$plg_id}");

        kleeja_admin_err(
            $ERR[$config['language']] ?? $ERR['en']
        );

        exit;
    }

    add_olang([
        'KJP_UPPACK_ERR'          => 'you don\'t have the permission to upload files until you have a valid subscription',
        'KJP_HELP_UPPACK'         => 'KJPay Upload Package' ,
        'KJP_HELP_UPPACK_CONTENT' => 'this plugin is a package for kleeja payment ,
        it gives upload permission for users who have a valid subscription only ,
        to active it , you have to active subscription system in kleeja payment'
    ] ,
    'en',
    $plg_id);
    add_olang([
        'KJP_UPPACK_ERR'          => 'ليس لديك إذن بتحميل الملفات حتى يكون لديك اشتراك صالح' ,
        'KJP_HELP_UPPACK'         => 'حزم الرفع للموقع' ,
        'KJP_HELP_UPPACK_CONTENT' => 'هذالاضافة هي حزمة لمدفوعات كليجا ،   يمنح إذن التحميل للمستخدمين الذين لديهم اشتراك صالح فقط ، لتنشيط هذه الاضافة ، يجب عليك تفعيل نظام الاشتراك في مدفوعات كليجا'
    ] ,
    'ar',
    $plg_id);
};


//Plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['kjp_upload_package']['update'] = function ($old_version, $new_version) {
};


// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['kjp_upload_package']['uninstall'] = function ($plg_id) {
    delete_olang(null, ['ar', 'en'], $plg_id);
};


// Plugin functions
$kleeja_plugin['kjp_upload_package']['functions'] = [
    'begin_index_page' => function ($args) {
        global $config , $subscription , $olang , $usrcp;

        if ($config['kjp_active_subscriptions'] && defined('IN_SUBMIT_UPLOADING') && IN_SUBMIT_UPLOADING)
        {
            if ($usrcp->group_id() !== '1' && ! $subscription->is_valid($usrcp->id()))
            {
                kleeja_err($olang['KJP_UPPACK_ERR']);

                exit;
            }
        }
    },
    'end_common' => function ($args) {
        if (! defined('IN_ADMIN'))
        {
            $d_groups = $args['d_groups'];
            unset($d_groups[2]);
            return compact('d_groups');
        }
    },
    'KjPay:KLJ_HELP' => function ($args) {
        global $olang;
        $KJP_HELP = $args['KJP_HELP'];
        $KJP_HELP[] = 
        [
            'ID'      => 'KJP_UPPACK' ,
            'TITLE'   => $olang['KJP_HELP_UPPACK'] ,
            'CONTENT' => $olang['KJP_HELP_UPPACK_CONTENT']
        ];
        return compact('KJP_HELP');
    },
];
