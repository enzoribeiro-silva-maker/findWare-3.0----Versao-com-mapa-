<?php
class ArduinoController extends Controller
{
    public function index(): void
    {
        $this->view('arduino/index');
    }
}
