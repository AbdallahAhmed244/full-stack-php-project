<?php

function requireLogin()
{
    if (!isset($_SESSION['user_id']))
    {
        header("Location: login.php");
        exit;
    }
}

function requireRole($role)
{
    requireLogin();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role)
    {
        header("Location: 403.php");
        exit;
    }
}

function requireAnyRole($roles)
{
    requireLogin();

    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles))
    {
        header("Location: 403.php");
        exit;
    }
}