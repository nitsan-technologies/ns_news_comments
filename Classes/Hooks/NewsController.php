<?php
namespace Nitsan\NsNewsComments\Hooks;

session_start();
class NewsController
{
    public function modify(array $params)
    {
        $_SESSION['params'] = $params;
        $settings = $params['originalSettings'];
        return $settings;
    }
}
