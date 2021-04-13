<?php
/**
 * Conexión a LDAP
 *
 * Conexión al protocolo LDAP.
 *
 * @copyright  2017 - Diego Soba.
 * @author     Diego Soba <didom900@gmail.com>
 * @version    v1.0.0
 */

namespace contSoft\Finanzas\Clases\Auth;

use contSoft\Finanzas\Clases\Auth\ConectorInterface;

class LdapConnector implements ConectorInterface
{
    private $ldapCon;
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
        $this->conectar();
    }

    public function conectar()
    {
        $connect = ldap_connect($this->conexion['LDAP_HOST'], $this->conexion['LDAP_PORT']);

        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

        ldap_bind($connect, 'CN=' . $this->conexion['LDAP_ADMIN'] . ',' . $this->conexion['LDAP_DOMAIN'], $this->conexion['LDAP_PASS']);
        $this->ldapCon = $connect;
    }

    public function getLDAPCon()
    {
        return $this->ldapCon;
    }

    public function getDominioLDAP()
    {
        return $this->conexion['LDAP_DOMAIN'];
    }

    public function __destruct()
    {
        ldap_close($this->ldapCon);
    }

    /**
     * Devuelve true si el usuario existe o false en caso
     * contrario.
     *
     * @param  string $usuario
     * @param  string $password
     * @param  int $empresa
     * @return bool
     */
    public function authUser($usuario, $password, $empresa)
    {
        if (($res_id = ldap_search($this->getLDAPCon(), $this->getDominioLDAP(), '(sAMAccountName='.$usuario.')')) == false) {
            return false;
        }
        //dd($res_id);

        if (($entry_id = ldap_first_entry($this->getLDAPCon(), $res_id)) == false) {
            return false;
        }
        //($entry_id);

        if (($user_dn = ldap_get_dn($this->getLDAPCon(), $entry_id)) == false) {
            return false;
        }
        dd($user_dn);

        if (($link_id = ldap_bind($this->getLDAPCon(), $user_dn, $password)) == false) {
            return false;
        } else {
            return true;
        }
    }
}
