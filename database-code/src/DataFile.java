import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.RandomAccessFile;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashMap;

public class DataFile {
	int FILE_SIZE=512*10+3*4+10;
	int[] pageArray;
	int numOfFields;
	int sizeOfARecord;
	int fileId;
	int pointerToNumOfUsedPages = 0;
	int pointerToNextFile = 4;
	int pointerToPrevFile = 8;
	int pointerToPageUsedArray = 12;
	int pointerToPageFullArray =22;
	int pointerToPageDataStart = 32;
	RandomAccessFile realFile;
	SystemCatalog sysCat;
	
	int firstEmptySlotOffsetInPage = 0;
	int endOfUsedSpaceOffsetInPage = 4;
	int numOfRecordsKeptOffsetInPage = 8;
	int recordsKeptInPageOffset=12;
	
	HashMap<Integer,RandomAccessFile> realFileMap;
	
	public DataFile(RandomAccessFile file, boolean fileInitialized, int numOfFields, SystemCatalog sysCat, int fileId) throws IOException{
		this.pageArray = new int[10];
		for(int i=0;i<10;i++){
			this.pageArray[i]=pointerToPageDataStart+i*512;
		}
		this.realFile = file;
		this.sysCat = sysCat;
		this.fileId=fileId;
		realFile.setLength(FILE_SIZE);
		this.numOfFields=numOfFields;
		this.sizeOfARecord = 1+ 4*numOfFields;
		if(!fileInitialized) initializeRandomAccessFile(realFile);
		this.realFileMap = new HashMap<Integer,RandomAccessFile>();
		realFileMap.put(fileId, file);
	}
	
	public void addRecord(ArrayList<Integer> fieldValues) throws IOException{
		//If the given number of field values is not enough, there must be an error.
		if(fieldValues.size() != numOfFields){
			System.out.println("Error, the input is not valid");
			return;
		}
		//Hash function to determine the page that the record will be stored.
		int key = fieldValues.get(0);
		key = ((key%10)+10)%10;
		int pageOffset = pageArray[key];
		RandomAccessFile file = realFile;
		file.seek(pointerToPageFullArray+key);
		int currentFileId=this.fileId;
		//Seeks for a non-full page to put the record in.
		while(file.readBoolean()){
			file.seek(pointerToNextFile);
			int nextFileId =file.readInt();
			if(nextFileId<0){
				nextFileId = sysCat.getIdToOpenFile();
				RandomAccessFile next = new RandomAccessFile(nextFileId+".txt","rw");
				initializeRandomAccessFile(next);
				file.seek(pointerToNextFile);
				file.writeInt(nextFileId);
				file = next;
				file.seek(pointerToPrevFile);
				file.writeInt(currentFileId);
				realFileMap.put(nextFileId, next);
			}
			else{
				file =getRandomAccessFileWithId(nextFileId);
			}
			currentFileId = nextFileId;			
		}
		file.seek(pageOffset);
		int firstEmptySlot=file.readInt();
		int endOfUsedSpace=file.readInt(); 
		//Record is written to the found page.
		writeRecord(fieldValues , file, firstEmptySlot+pageOffset);
		firstEmptySlot+=sizeOfARecord;
		//Update the first empty slot values in the page header.
		if(firstEmptySlot>endOfUsedSpace){
			endOfUsedSpace = firstEmptySlot;
			file.seek(pageOffset);
			file.writeInt(endOfUsedSpace);
			file.writeInt(endOfUsedSpace);
		}
		else{
			file.seek(firstEmptySlot+pageOffset);
			while(firstEmptySlot<endOfUsedSpace && file.readBoolean()){
				firstEmptySlot+=sizeOfARecord;
				file.seek(firstEmptySlot+pageOffset);
			}
		}
		file.seek(firstEmptySlotOffsetInPage+pageOffset);
		file.writeInt(firstEmptySlot);
		file.seek(endOfUsedSpaceOffsetInPage+pageOffset);
		file.writeInt(endOfUsedSpace);
		//Updates the page used array in case of a change. 
		file.seek(pointerToPageUsedArray+key);
		boolean pageUsed = file.readBoolean();
		if(!pageUsed){
			file.seek(pointerToNumOfUsedPages);
			int usedPages = file.readInt()+1;
			file.seek(pointerToNumOfUsedPages);
			file.writeInt(usedPages);
		}
		//Updates the page full array if the current page can not take another record.
		file.seek(pointerToPageUsedArray+key);
		file.writeBoolean(true);
		if(firstEmptySlot+sizeOfARecord>512){
			file.seek(pointerToPageFullArray+key);
			file.writeBoolean(true);
		}
	}
	
	public void deleteRecord(int key) throws IOException{
		int currentFileId = this.fileId;
		int recordAddress = -1;
		RandomAccessFile file = getRandomAccessFileWithId(currentFileId);
		//Traverse all the files of the type and find the file and the address of the record.
		while(recordAddress < 0 && currentFileId >=0){
			file = getRandomAccessFileWithId(currentFileId);
			recordAddress = findRecordInFile(file,key);
			file.seek(pointerToNextFile);
			currentFileId = file.readInt();
		}
		//Turn the valid bit of the record to false
		file.seek(recordAddress);
		file.writeBoolean(false);
		int hash = ((key%10)+10)%10;
		int pageOffset = pageArray[hash];
		file.seek(pageOffset + recordsKeptInPageOffset);
		//Get how many records are kept at the page and decrease it by one
		int n = file.readInt() -1;
		file.seek(pageOffset + recordsKeptInPageOffset);
		file.writeInt(n);
		//If there is no records left in the page, turn the boolean false in page used array.
		if(n == 0){
			//If there is no records left in the page, turn the boolean false in page used array.
			file.seek(pointerToPageUsedArray+hash);
			file.writeBoolean(false);
			//Check the page used array and find out if the file is used.
			boolean isFileUsed = false;
			for(int i =0 ;i<10;i++){
				file.seek(pointerToPageUsedArray+i);
				if(file.readBoolean()){
					isFileUsed = true;
				}
			}
			//If file is not used anymore delete the file.
			if(!isFileUsed){
				file.seek(pointerToPrevFile);
				int prev = file.readInt();
				file.seek(pointerToNextFile);
				int next = file.readInt();
				//Change the previous file's next pointer.
				if(prev >=0){
					RandomAccessFile prevFile = getRandomAccessFileWithId(prev);
					prevFile.seek(pointerToNextFile);
					prevFile.writeInt(next);
				}
				//Change the next file's prev pointer.
				if(next >=0){
					RandomAccessFile nextF = getRandomAccessFileWithId(next);
					nextF.seek(pointerToPrevFile);
					nextF.writeInt(prev);
				}
				//Delete the file from realFileMap
				realFileMap.remove(currentFileId);
				//Delete the file from the computer
				file.close();
				File delete = new File(currentFileId +".txt");
				if(!delete.delete()) System.out.println("An error happened while deleting file "+currentFileId+".txt");
				
				//Exit the method as there is nothing left to do
				return;
			}
		}
		//Change the page full array value to false as the page is not full anymore
		file.seek(pointerToPageFullArray+hash);
		file.writeBoolean(false);
		//Update the first empty slot and end of the file value if necessary
		file.seek(pageOffset+firstEmptySlotOffsetInPage);
		//If the first empty slot value that is kept in page is larger than records address, update
		if(pageOffset + file.readInt() > recordAddress){
			file.seek(pageOffset+firstEmptySlotOffsetInPage);
			file.writeInt(recordAddress - pageOffset);
		}
		file.seek(pageOffset+endOfUsedSpaceOffsetInPage);
		//If the end of the page used area value that is kept in page is records end, update
		if(pageOffset + file.readInt() == recordAddress + sizeOfARecord){
			file.seek(pageOffset+endOfUsedSpaceOffsetInPage);
			file.writeInt(recordAddress - pageOffset);
		}
	}
	
	public ArrayList<Record> listRecords() throws IOException{
		ArrayList<Record> records = new ArrayList<Record>();
		int currentFileId = fileId;
		int count = 0;
		//Traversing all the files of the same type
		while(currentFileId >= 0){
			RandomAccessFile file = getRandomAccessFileWithId(currentFileId);
			//Traverse the pages.
			for(int i =0;i<10;i++){
				//See if the page is used.
				file.seek(pointerToPageUsedArray+i);
				int pageOffset = pageArray[i];
				//If the page i is used, then check its inside to list records.
				if(file.readBoolean()){
					int pagePointer = recordsKeptInPageOffset;
					file.seek(endOfUsedSpaceOffsetInPage+pageOffset);
					int endOfPage = file.readInt();
					while(pagePointer < endOfPage){
						file.seek(pageOffset + pagePointer);
						//If the record is valid
						if(file.readBoolean()){
							//Get its key and other values and create a Record object for it.
							int key = file.readInt();
							int[] values = new int[numOfFields -1];
							for(int j = 0;j<numOfFields -1;j++){
								values[j]=file.readInt();
							}
							//Add the record to records list
							records.add(new Record(key,values));
							count++;
						}
						pagePointer += sizeOfARecord;
					}
				}
			}
			//After traversing all the pages, jump to the next page
			file.seek(pointerToNextFile);
			currentFileId = file.readInt();
		}
		Collections.sort(records);
		return records;
	}
	
	public void updateRecord(ArrayList<Integer> fieldValues) throws IOException{
		int currentFileId = this.fileId;
		int recordAddress = -1;
		RandomAccessFile file = getRandomAccessFileWithId(currentFileId);
		//Traverse all the files of the type and find the file and the address of the record.
		while(recordAddress < 0 && currentFileId >=0){
			file = getRandomAccessFileWithId(currentFileId);
			recordAddress = findRecordInFile(file,fieldValues.get(0));
			file.seek(pointerToNextFile);
			currentFileId = file.readInt();
		}
		file.seek(recordAddress+1+4);
		for(int i=1;i<fieldValues.size();i++){
			file.writeInt(fieldValues.get(i));
		}
	}
	
	public Record searchRecord(int key) throws IOException{
		int currentFileId = this.fileId;
		int recordAddress = -1;
		RandomAccessFile file = getRandomAccessFileWithId(currentFileId);
		//Traverse all the files of the type and find the file and the address of the record.
		while(recordAddress == -1 && currentFileId >=0){
			file = getRandomAccessFileWithId(currentFileId);
			recordAddress = findRecordInFile(file,key);
			file.seek(pointerToNextFile);
			currentFileId = file.readInt();
		}
		//If the record address is -1 ,this means the record does not exists.
		if(recordAddress == -1){
			//In that case, return null to indicate that the record doesn't exist
			return null;
		}else{
			file.seek(recordAddress+5);
			int[] values = new int[numOfFields-1];
			for(int i=0;i<numOfFields-1;i++){
				values[i]=file.readInt();
			}
			return new Record(key,values);
		}
	}
	
	private RandomAccessFile getRandomAccessFileWithId(int fileId) throws FileNotFoundException{
		if(realFileMap.containsKey(fileId)){
			return realFileMap.get(fileId);
		}else{
			RandomAccessFile file = new RandomAccessFile(fileId+".txt","rw");
			realFileMap.put(fileId, file);
			return file;
		}
	}
	
	//Finds the given record in given file and returns its position in the file. If not found, returns -1
	private int findRecordInFile(RandomAccessFile file,int key) throws IOException{
		int hash = ((key%10)+10)%10;
		int pageOffset = pageArray[hash];
		int pointer = recordsKeptInPageOffset;
		file.seek(pageOffset+endOfUsedSpaceOffsetInPage);
		int endOfPage = file.readInt();
		while(pointer < endOfPage){
			file.seek(pointer+pageOffset);
			if(file.readBoolean()){
				if(file.readInt() == key){
					return pointer+pageOffset;
				}
			}
			pointer += sizeOfARecord;
		}
		return -1;
	}
	
	//Writes given field values to the given position as record
	private void writeRecord(ArrayList<Integer> fieldValues, RandomAccessFile file , int recordOffset) throws IOException{
		file.seek(recordOffset);
		file.writeBoolean(true);
		for(int i=0;i<this.numOfFields;i++){
			file.writeInt(fieldValues.get(i));
		}
	}
	
	private void initializeRandomAccessFile(RandomAccessFile randomAccess) throws IOException{
		randomAccess.seek(pointerToNumOfUsedPages);
		//Initialize number of used pages
		randomAccess.writeInt(0);
		//Initialize next file id
		randomAccess.writeInt(-1);
		//Initialize previous file id
		randomAccess.writeInt(-1);
		//Initialize pageUsedArray and pageFullArray
		for(int i=0;i<20;i++){
			randomAccess.writeBoolean(false);
		}
		//Initializes the pages in the file
		for(int i=0;i<10;i++){
			int pageStart = pageArray[i];
			randomAccess.seek(pageStart);
			randomAccess.writeInt(recordsKeptInPageOffset);
			randomAccess.writeInt(recordsKeptInPageOffset);
			randomAccess.writeInt(0);
		}
	}
}

