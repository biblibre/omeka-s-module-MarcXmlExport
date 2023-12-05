<?php

namespace MarcXmlExport\Controller\Admin;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Omeka\Stdlib\Message;
use MarcXmlExport\Form\ExportForm;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel;
        $page = $this->params()->fromQuery('page', 1);
        $query = $this->params()->fromQuery() + [
            'page' => $page,
            'sort_by' => $this->params()->fromQuery('sort_by', 'id'),
            'sort_order' => $this->params()->fromQuery('sort_order', 'desc'),
        ];
        $response = $this->api()->search('marc_xml_export_exports', $query);
        $this->paginator($response->getTotalResults(), $page);
        $view->setVariable('exports', $response->getContent());

        return $view;
    }

    public function newAction()
    {
        $form = $this->getForm(ExportForm::class);

        $view = new ViewModel();
        $view->setVariable('form', $form);

        return $view;
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('admin/marcxml-export');
        }

        $post = $request->getPost()->toArray();

        unset($post['csrf']);
        $args = $post;

        $dispatcher = $this->jobDispatcher();
        $job = $dispatcher->dispatch('MarcXmlExport\Job\ExportJob', $args);

        $message = new Message(
            'Exporting in background (%sjob #%d%s)', // @translate
                sprintf(
                    '<a href="%s">',
                    htmlspecialchars($this->url()->fromRoute('admin/id', ['controller' => 'job', 'id' => $job->getId()]))
                ),
            $job->getId(),
            '</a>'
        );
        $message->setEscapeHtml(false);
        $this->messenger()->addSuccess($message);

        return $this->redirect()->toRoute('admin/marcxml-export');
    }
}
