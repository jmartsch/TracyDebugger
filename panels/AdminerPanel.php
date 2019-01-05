<?php

class AdminerPanel extends BasePanel {

    protected $icon;

    public function getTab() {

        if(\TracyDebugger::isAdditionalBar()) return;
        \Tracy\Debugger::timer('adminer');

        $this->icon = '
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="304.4 284.4 11.7 16">
            <path fill="'.\TracyDebugger::COLOR_NORMAL.'" d="M304.4 294.8v2.3c.3 1.3 2.7 2.3 5.8 2.3s5.7-1 5.9-2.3v-2.3c-1 .8-3.1 1.4-6 1.4-2.8 0-4.8-.6-5.7-1.4zM310.7 291.9h-1.2c-1.7-.1-3.1-.3-4-.7-.4-.2-.9-.4-1.1-.6v2.4c.7.8 2.9 1.5 5.8 1.5 3 0 5.1-.7 5.8-1.5v-2.4c-.3.2-.7.5-1.1.6-1.1.4-2.5.6-4.2.7zM310.1 285.6c-3.5 0-5.5 1.1-5.8 2.3v.7c.7.8 2.9 1.5 5.8 1.5s5.1-.7 5.8-1.5v-.6c-.3-1.3-2.3-2.4-5.8-2.4z"/>
        </svg>';

        return '
        <span title="Adminer">' .
            $this->icon . (\TracyDebugger::getDataValue('showPanelLabels') ? '&nbsp;Adminer' : '') . '
        </span>';
    }


    public function getPanel() {

        $adminerModuleId = $this->wire('modules')->getModuleID("ProcessTracyAdminer");
        $adminerUrl = $this->wire('pages')->get("process=$adminerModuleId")->url;
        $contextLink = '';

        if(\TracyDebugger::getDataValue('referencePageEdited') && $this->wire('input')->get('id') &&
            ($this->wire('process') == 'ProcessPageEdit' ||
                $this->wire('process') == 'ProcessUser' ||
                $this->wire('process') == 'ProcessRole' ||
                $this->wire('process') == 'ProcessPermission'
            )
        ) {
            $p = $this->wire('process')->getPage();
        }
        elseif($this->wire('process') == 'ProcessPageView') {
            $p = $this->wire('page');
        }

        if(isset($p)) {
            $contextLink = '&edit=pages&where%5Bid%5D='.$p->id;
        }
        elseif($this->wire('process') == 'ProcessPageList') {
            $contextLink = '&select=pages';
        }
        elseif($this->wire('page')->process == 'ProcessField') {
            if($this->wire('input')->get('id')) {
                $contextLink = '&edit=fields&where%5Bid%5D='.(int)$this->wire('input')->get('id');
            }
            else {
                $contextLink = '&select=fields';
            }
        }
        elseif($this->wire('page')->process == 'ProcessTemplate') {
            if($this->wire('input')->get('id')) {
                $contextLink = '&edit=templates&where%5Bid%5D='.(int)$this->wire('input')->get('id');
            }
            else {
                $contextLink = '&select=templates';
            }
        }
        elseif($this->wire('page')->process == 'ProcessModule') {
            if($this->wire('input')->get('name')) {
                $contextLink = '&edit=modules&where%5Bclass%5D='.$this->wire('sanitizer')->name($this->wire('input')->get('name'));
            }
            else {
                $contextLink = '&select=modules';
            }
        }

        $out = '
        <h1>' . $this->icon . ' Adminer</h1><span class="tracy-icons"><span class="resizeIcons"><a href="#" title="Maximize / Restore" onclick="tracyResizePanel(\'AdminerPanel\')">+</a></span></span>';

        if($this->wire('modules')->isInstalled("ProcessTracyAdminer")) {
            $out .= '
            <div class="tracy-inner" style="padding: 0 !important">
                <iframe src="'.$adminerUrl.'?iframe=1'.$contextLink.'" style="width:100%; height:calc(100% - 5px); border: none; padding:0; margin:0;"></iframe>';
        }
        else {
            $out .= '
            <div class="tracy-inner">
                <p>This panel is not available because the ProcessTracyAdminer module has not been installed.</p>';
        }

        $out .= '<div style="padding-left:5px">'.\TracyDebugger::generatePanelFooter('adminer', \Tracy\Debugger::timer('adminer'), strlen($out), 'adminerPanel').'</div>';
        $out .= '
        </div>';

        return parent::loadResources() . $out;
    }

}
