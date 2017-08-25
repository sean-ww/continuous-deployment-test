<?php

namespace CD;

use \CD\services\GitHubUsersService;

/**
 * Application Class
 *
 * For retrieving Sean's GitHub data and rendering to a template.
 *
 * @author Sean Wallis <sean.wallis2@networkrail.co.uk>
 */
class App
{
    /** @var \Twig_Environment $view Twig template engine environment object. */
    private $view;

    /** @var array $data Sean's GitHub User Data. */
    public $data;

    /**
     * App constructor - Pull in app dependencies and retrieve data.
     *
     * @param \Twig_Environment  $view               Twig template engine environment object.
     * @param GitHubUsersService $gitHubUsersService A service for retrieving GitHub user data.
     */
    public function __construct(\Twig_Environment $view, GitHubUsersService $gitHubUsersService)
    {
        $this->view = $view;
        $this->data = $gitHubUsersService->retrieveData('sean-ww');
    }

    /**
     * Get an instance of the application.
     *
     * @return $this An instance of the application.
     */
    public function getInstance()
    {
        return $this;
    }

    /**
     * Render the application
     *
     * @return string A twig template.
     */
    public function render()
    {
        return $this->view->render('index.html', ['data' => $this->data]);
    }
}
