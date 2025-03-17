<?php

class Controller {
    /**
     * Load a model and return its instance.
     *
     * @param string $model Name of the model to load.
     * @return object       Instance of the model.
     */
    public function loadModel($model) {
        $modelPath = "../models/$model.php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model();
        } else {
            die("Model $model not found.");
        }
    }

    /**
     * Load a view and pass data to it.
     *
     * @param string $view Name of the view file.
     * @param array $data  Data to pass to the view.
     * @return void
     */
    protected function view($view, $data = []) {
        extract($data);
        require_once 'views/' . $view . '.php';
    }
    protected function frontView($view, $data = []) {
        require_once 'views/' . $view . '.php';
    }

    /**
     * Redirect to a different URL.
     *
     * @param string $url The URL to redirect to.
     * @return void
     */
    public function redirect($url) {
        header("Location: $url");
        exit();
    }

    public function home() {
        $this->view('home'); // This will load views/home.php
    }
}
