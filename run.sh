#!/bin/bash
OLD_HTTPD_PIDS="$(ps -fu root |grep "firefox" | awk '{print $2}')"

for FPID in ${OLD_HTTPD_PIDS}
do
#echo "Killing firefox processe pid: ${FPID}"
kill -s 9 ${FPID}
done
npm run dev
