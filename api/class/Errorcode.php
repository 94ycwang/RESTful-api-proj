<?php

class Errorcode
{
    // user
    const USERNAME_CANNOT_NULL = 001;
    const USERPASS_CANNOT_NULL = 002;
    const USERNAME_EXIST = 003;
    const REGISTER_FAIL = 004;
    const LOGIN_FAIL = 005;
    const USERNAME_OR_PASSWORD_ERROR = 006;

    // article
    const ARTICLE_TITLE_CANNOT_NULL = 101;
    const ARTICLE_CONTENT_CANNOT_NULL = 102;
    const ARTICLE_CREATE_FAIL = 103;
    const ARTICLE_ID_CANNOT_NULL = 104;
    const ARTICLE_GET_FAIL = 105;
    const ARTICLE_NOT_EXIST = 106;
    const PERMISSION_NOT_ALLOW = 107;
    const ARTICLE_EDIT_FAIL = 108;
    const ARTICLE_DELETE_FAIL = 108;
}
