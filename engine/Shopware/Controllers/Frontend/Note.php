<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

class Shopware_Controllers_Frontend_Note extends Enlight_Controller_Action
{
    /**
     * Pre dispatch method
     */
    public function preDispatch()
    {
        $this->View()->setScope(Enlight_Template_Manager::SCOPE_PARENT);
        $this->View()->assign('userInfo', $this->get('shopware_account.store_front_greeting_service')->fetch());
    }

    public function postDispatch()
    {
        $session = $this->get('session');

        $session->offsetSet('sNotesQuantity', $this->get('modules')->Basket()->sCountNotes());

        // Update note userID
        $userId = $session->get('sUserId');
        $uniqueId = $this->Request()->getCookie('sUniqueID');

        if (!empty($userId) && !empty($uniqueId)) {
            $this->get('dbal_connection')->executeQuery('UPDATE s_order_notes SET userID = :userId WHERE sUniqueID = :uniqueId AND userID = 0',
                [
                    'userId' => $userId,
                    'uniqueId' => $uniqueId,
                ]);
        }
    }

    public function indexAction()
    {
        $view = $this->View();
        $view->assign('sNotes', $this->get('modules')->Basket()->sGetNotes());
        $view->assign('sUserLoggedIn', $this->get('modules')->Admin()->sCheckUser());
        $view->assign('sOneTimeAccount', $this->get('session')->offsetGet('sOneTimeAccount'));
    }

    public function deleteAction()
    {
        if (!empty($this->Request()->sDelete)) {
            $this->get('modules')->Basket()->sDeleteNote($this->Request()->sDelete);
        }
        $this->forward('index');
    }

    public function addAction()
    {
        $orderNumber = $this->Request()->getParam('ordernumber');

        if ($this->addNote($orderNumber)) {
            $this->View()->assign('sArticleName', $this->get('modules')->Articles()->sGetArticleNameByOrderNumber($orderNumber));
        }

        $this->forward('index');
    }

    public function ajaxAddAction()
    {
        $this->Request()->setHeader('Content-Type', 'application/json');
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $this->Response()->setBody(json_encode(
            [
                'success' => $this->addNote($this->Request()->getParam('ordernumber')),
                'notesCount' => (int) $this->get('modules')->Basket()->sCountNotes(),
            ]
        ));
    }

    private function addNote($orderNumber)
    {
        if (empty($orderNumber)) {
            return false;
        }

        $productId = $this->get('modules')->Articles()->sGetArticleIdByOrderNumber($orderNumber);
        $productName = $this->get('modules')->Articles()->sGetArticleNameByOrderNumber($orderNumber);

        if (empty($productId)) {
            return false;
        }

        $this->get('modules')->Basket()->sAddNote($productId, $productName, $orderNumber);

        return true;
    }
}
