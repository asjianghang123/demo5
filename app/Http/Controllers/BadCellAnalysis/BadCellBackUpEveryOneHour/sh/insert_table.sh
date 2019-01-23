#!/bin/sh

mysql -uroot -pmongs <<EOF > /dev/null 2>&1
	use AutoKPI
	TRUNCATE TABLE LowAccessCellTableEveryOneHour;
	INSERT INTO LowAccessCellTableEveryOneHour SELECT * FROM temp_lowaccesscell;
	TRUNCATE TABLE HighLostCellTableEveryOneHour;
	INSERT INTO HighLostCellTableEveryOneHour SELECT * FROM temp_highlostcell;
	TRUNCATE TABLE BadHandoverCellTableEveryOneHour;
	INSERT INTO BadHandoverCellTableEveryOneHour SELECT * FROM temp_badhandovercell;
	TRUNCATE TABLE NeighBadHandoverCellTableEveryOneHour;
	INSERT INTO NeighBadHandoverCellTableEveryOneHour SELECT * FROM temp_neigh;
EOF

wait
echo "OK"
exit 0
