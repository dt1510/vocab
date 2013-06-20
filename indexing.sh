#indexing
words=`cat vl.txt`
for word in $words
do
    touch data/$word.txt
done
