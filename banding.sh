find . -type f  | xargs ls --full-time -r -oAHd  | awk '{ print "{\"file\":\""$8"\",\"tanggal\":\""$6"-"$5"-"$7"\",\"size\":\""$4"\"} "}'
find . -type f -printf "%-.22T+ %M %n %-8u %-8g %8s %Tx %.8TX %p\n" | sort | cut -f 8- -d ' '| awk '{ print "{\"file\":\""$5"\",\"tanggal\":\""$3"-"$4"\",\"size\":\""$2"\"} "}'
