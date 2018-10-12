<?php
/**
 * Created by PhpStorm.
 * User: Richie
 * Date: 9/14/2018
 * Time: 7:38 PM
 */


class db_connection
{
    private $db_conn;

    function db_connection($dbServerName, $dbUserName, $dbPassword, $dbName)
    {
        $this->db_conn = mysqli_connect($dbServerName, $dbUserName, $dbPassword, $dbName) or die(mysqli_connect_error());
    }

    function query($sqlQuery, $debugger = 0)
    {
        $result = $this->db_conn->query($sqlQuery);
        if ($debugger == 1) {
            $this->query_debugger($result, $sqlQuery);
        }
        return $result;
    }

    function fetch_data($result){
        $row_content = [];
        while ($row = mysqli_fetch_assoc($result)){
            $row_content[] = $row;
        }
        return $row_content;
    }

    function num_of_rows($result){
        return mysqli_num_rows($result);
    }

    function get_last_insert_id(){
        return mysqli_insert_id($this->db_conn);
    }

    private function query_debugger($result, $sqlQuery)
    {
        $htmlContent = "<html><head><style>
                        table {
                              border-spacing: 1;
                              border-collapse: collapse;
                              background: white;
                              border-radius: 6px;
                              overflow: hidden;
                              max-width: 800px;
                              width: 100%;
                              margin: 0 auto;
                              position: relative;
                            }
                            table * {
                              position: relative;
                            }
                            table td, table th {
                              padding-left: 8px;
                            }
                            table thead tr {
                              height: 60px;
                              background: #FFED86;
                              font-size: 16px;
                            }
                            table tbody tr {
                              height: 48px;
                              border-bottom: 1px solid #E3F1D5;
                            }
                            table tbody tr:last-child {
                              border: 0;
                            }
                            table td, table th {
                              text-align: left;
                            }
                            table td.l, table th.l {
                              text-align: right;
                            }
                            table td.c, table th.c {
                              text-align: center;
                            }
                            table td.r, table th.r {
                              text-align: center;
                            }
                            
                            @media screen and (max-width: 35.5em) {
                              table {
                                display: block;
                              }
                              table > *, table tr, table td, table th {
                                display: block;
                              }
                              table thead {
                                display: none;
                              }
                              table tbody tr {
                                height: auto;
                                padding: 8px 0;
                              }
                              table tbody tr td {
                                padding-left: 45%;
                                margin-bottom: 12px;
                              }
                              table tbody tr td:last-child {
                                margin-bottom: 0;
                              }
                              table tbody tr td:before {
                                position: absolute;
                                font-weight: 700;
                                width: 40%;
                                left: 10px;
                                top: 0;
                              }
                              table tbody tr td:nth-child(1):before {
                                content: \"Code\";
                              }
                              table tbody tr td:nth-child(2):before {
                                content: \"Stock\";
                              }
                              table tbody tr td:nth-child(3):before {
                                content: \"Cap\";
                              }
                              table tbody tr td:nth-child(4):before {
                                content: \"Inch\";
                              }
                              table tbody tr td:nth-child(5):before {
                                content: \"Box Type\";
                              }
                            }
                            body {
                              background: #AAA;
                              font: 400 14px 'Calibri','Arial';
                              padding: 20px;
                            }
                            
                            blockquote {
                              color: white;
                              text-align: center;
                            }
                    </style></head><body><table ><tr><td colspan='2'></td></tr>";
        $htmlContent .= "<tr><td style='background-color: #01FF70;   color: white;'>SQL QUERY</td><td>" . $sqlQuery . "</td></tr>";
        $htmlContent .= "<tr><td style='background-color: #FF4136;   color: white;'>Error</td><td>" . mysqli_error($this->db_conn) . "</td></tr>";
        $htmlContent .= "<tr><td>Result:</td><td> Number of Rows:" . mysqli_num_rows($result) . "</td></tr><tr><td colspan='2' >";
        if (mysqli_num_rows($result)) {
            $i = 0;
            $htmlContent .= '<table>';
            $header_row = "<tr style='background-color: #39CCCC;   color: white;'>";
            $sql_result = "";
            while ($result_content = mysqli_fetch_assoc($result)){
                $sql_result .= "<tr>";
                foreach ($result_content as $key => $value) {
                    if ($i == 0) {
                        $header_row .= "<td>".$key."</td>";
                    }
                    $sql_result .= "<td>".$value."</td>";
                }
                $i++;
                $sql_result .= "</tr>";
            }
            $header_row .="</tr>";
            $htmlContent .= $header_row."".$sql_result."</table>";

        }

        $htmlContent .= "</td></tr></table></body></html>";

        echo $htmlContent;
        die();
    }
}