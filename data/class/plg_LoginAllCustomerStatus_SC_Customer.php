<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

require_once CLASS_REALDIR . 'SC_Customer.php';

class plg_LoginAllCustomerStatus_SC_Customer extends SC_Customer
{

    /**
     * @param string $email
     * @param string $pass
     */
    public function getCustomerDataFromEmailPass($pass, $email, $mobile = false)
    {
        $ret = parent::getCustomerDataFromEmailPass($pass, $email, $mobile);

        if ($ret === false) {
            // 小文字に変換
            $email = strtolower($email);
            $sql_mobile = $mobile ? ' OR email_mobile = ?' : '';
            $arrValues = array($email);
            if ($mobile) {
                $arrValues[] = $email;
            }
            // 本登録された会員のみ
            $sql = 'SELECT * FROM dtb_customer WHERE (email = ?' . $sql_mobile . ') AND del_flg = 0 AND status <> 1';
            $objQuery = & SC_Query_Ex::getSingletonInstance();
            $result = $objQuery->getAll($sql, $arrValues);
            if (empty($result)) {
                return false;
            } else {
                $data = $result[0];
            }

            // パスワードが合っていれば会員情報をcustomer_dataにセットしてtrueを返す
            if (SC_Utils_Ex::sfIsMatchHashPassword($pass, $data['password'], $data['salt'])) {
                $this->customer_data = $data;
                $this->startSession();

                return true;
            }

            return false;
        }
    }

}
