<?php

use Respect\Validation\Validator;

class ContactActions extends \Movim\Widget\Base
{
    function ajaxAddAsk($jid)
    {
        $view = $this->tpl();
        $view->assign('contact', App\Contact::firstOrNew(['id' => $jid]));
        $view->assign('groups', App\User::me()->session->contacts->pluck('group')->toArray());

        Dialog::fill($view->draw('_contactactions_add', true));
    }

    function ajaxGetDrawer($jid)
    {
        if (!$this->validateJid($jid)) return;

        $tpl = $this->tpl();
        $tpl->assign('contact', App\Contact::firstOrNew(['id' => $jid]));
        $tpl->assign('roster', App\User::me()->session->contacts->find($jid));

        /*$cr = $cd->getRosterItem($jid);

        if (isset($cr)) {
            if ($cr->value != null) {
                $tpl->assign('presence', getPresencesTxt()[$cr->value]);
            }

            $tpl->assign('contactr', $cr);
            $tpl->assign('caps', $cr->getCaps());
            $tpl->assign('clienttype', getClientTypes());
        } else {
            $tpl->assign('caps', null);
        }

        $c  = $cd->get($jid);
        $tpl->assign('contact', $c);*/

        Drawer::fill($tpl->draw('_contactactions_drawer', true));
    }

    function ajaxAdd($form)
    {
        $roster = new Roster;
        $roster->ajaxAdd($form);
    }

    function ajaxChat($jid)
    {
        if (!$this->validateJid($jid)) return;

        $c = new Chats;
        $c->ajaxOpen($jid);

        $this->rpc('MovimUtils.redirect', $this->route('chat', $jid));
    }

    /**
     * @brief Validate the jid
     *
     * @param string $jid
     */
    private function validateJid($jid)
    {
        $validate_jid = Validator::stringType()->noWhitespace()->length(6, 60);
        return ($validate_jid->validate($jid));
    }
}
