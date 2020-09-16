<?php
/**
 *    Client Base API Handler
 *    Copyright (C) 2020  Dmitry Shumilin (dr.noisier@yandex.ru)
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
namespace ClientBaseAPI;

interface HandlerInterface
{

    /**
     * Handler constructor.
     * 
     * @param string $url
     * @param string $login
     * @param string $key
     * @return Handler
     */
    public function __construct(string $url, string $login, string $key);

    /**
     * Method for creating data in table.
     * 
     * @param array $command
     * @return mixed
     */
    public function dataCreate(array $command);

    /**
     * Method for reading data in table.
     * 
     * @param array $command
     * @return mixed
     */
    public function dataRead(array $command);

    /**
     * Method for updating data in table.
     * 
     * @param array $command
     * @return mixed
     */
    public function dataUpdate(array $command);

    /**
     * Method for deleting data in table.
     * 
     * @param array $command
     * @return mixed
     */
    public function dataDelete(array $command);

    /**
     * A generic method for working with data in tables.
     * 
     * @param string $action
     * @param array $command
     * @return mixed
     */
    public function crud(string $action, array $command);

}
