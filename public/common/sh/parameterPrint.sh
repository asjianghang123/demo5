#!/bin/bash
database_host=127.0.0.1
database_username=root
database_password=mongs
dbname=$1

#login mysql console
mysql -h$database_host -u$database_username -p$database_password -N<< !

use $dbname

#source dbscript/load_data.sql
#source dbscript/create_index.sql
#source dbscript/create_views.sql

source /opt/mongs/mongs_all_parser/scripts/kget/procedure/create_Parameterindex.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterCellPrint_EUtranCellTDD.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterMeContextPrint_EUtranCellTDD.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterCellPrint_kgetCompare.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterMeContextPrint_kgetCompare.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterDrxProfilePrint_kgetCompare.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterNeighborPrint_kgetCompare.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterLTEFrequencyPrint_kgetCompare.sql;
source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameter2GFrequencyPrint_kgetCompare.sql;

source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameter2GFrequencyPrint_EUtranCellTDD.sql;

source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterLTEFrequencyPrint_EUtranCellTDD.sql;

source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterNeighborPrint_EUtranCellTDD.sql;

source /opt/mongs/mongs_all_parser/scripts/kget/procedure/parameterDrxProfilePrint_EUtranCellTDD.sql;



!
