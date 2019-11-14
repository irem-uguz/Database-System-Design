import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintStream;
import java.io.RandomAccessFile;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;

public class SystemCatalog {
	int SIZE_OF_TYPE_INFO = 97;
	RandomAccessFile systemCatalog;
	int OFFSET_OF_NUM_OF_TYPES = 0;
	int OFFSET_OF_FILE_IDS = 4;
	int OFFSET_OF_TYPE_DATA = 8;
	int OFFSET_OF_FILEPOINTER_IN_TYPEINFO = 1+8+4+80;
	HashMap<String,DataFile> typeFileMap;
	PrintStream output;
	
	public SystemCatalog(PrintStream output) throws IOException{
		this.systemCatalog = new RandomAccessFile("SystemCatalog.txt","rw");
		if(systemCatalog.length()==0){
			systemCatalog.seek(0);
			systemCatalog.writeInt(0);
			systemCatalog.writeInt(0);
		}
		this.typeFileMap = new HashMap<String,DataFile>();
		this.output = output;
	}
	
	/*Creates a type. Adds the information about the type at the end of the SystemCatalog file and updates 
	 * the number of types in the file. Assumes that the given type doesn't exist before.*/
	public void createType(String typeName, int numOfFields, String[] fieldNames) throws IOException{
		int currentPosition = 8;
		//First, looks for if there is a deleted type to override. If there is, takes its position and overrides.
		while(currentPosition < systemCatalog.length()){
			systemCatalog.seek(currentPosition);
			boolean isValid = systemCatalog.readBoolean();
			if(!isValid){
				break;
			} else{
				currentPosition += this.SIZE_OF_TYPE_INFO;
			}
		}
		systemCatalog.seek(currentPosition);
		typeName = fillWithSpace(typeName);
		systemCatalog.writeBoolean(true);
		systemCatalog.writeBytes(typeName);
		systemCatalog.writeInt(numOfFields);
		for(int i=0;i<numOfFields ;i++){
			systemCatalog.writeBytes(fillWithSpace(fieldNames[i]));
		}
		for(int i=0;i<10-numOfFields;i++){
			systemCatalog.writeBytes(fillWithSpace(""));
		}
		//At the moment the type is created the file pointer is meaningless as there is no file yet.
		systemCatalog.writeInt(-1);
		systemCatalog.seek(OFFSET_OF_NUM_OF_TYPES);
		int n = systemCatalog.readInt();
		systemCatalog.seek(OFFSET_OF_NUM_OF_TYPES);
		systemCatalog.writeInt(n+1);
		System.out.println("Successfully created type "+typeName);
	}
	
	public void deleteType(String typeName) throws IOException{
		int currentPosition = 8;
		typeName = fillWithSpace(typeName);
		//Looks for all the valid types in the systemCatalog file.
		while(currentPosition < systemCatalog.length()){
			systemCatalog.seek(currentPosition);
			boolean isValid = systemCatalog.readBoolean();
			if(isValid){
				byte[] nameBytes = new byte[8];
				systemCatalog.read(nameBytes);
				String name = new String(nameBytes);
				if(name.equals(typeName)){
					systemCatalog.seek(OFFSET_OF_NUM_OF_TYPES);
					int num = systemCatalog.readInt();
					systemCatalog.seek(OFFSET_OF_NUM_OF_TYPES);
					systemCatalog.writeInt(num-1);
					systemCatalog.seek(currentPosition);
					systemCatalog.writeBoolean(false);
					systemCatalog.seek(currentPosition+OFFSET_OF_FILEPOINTER_IN_TYPEINFO);
					int fileId = systemCatalog.readInt();
					while(fileId>=0){
						RandomAccessFile file;
						if(typeFileMap.containsKey(typeName)){
							file = typeFileMap.get(typeName).realFile;
							typeFileMap.remove(typeName);
						}
						else{
							file = new RandomAccessFile(fileId+".txt","rw");
						}
						file.seek(4);
						int nextFile = file.readInt();
						file.close();
						File delete = new File(fileId+".txt");
						if(!delete.delete()) System.out.println("An error happened while deleting file "+fileId+".txt");
						fileId = nextFile;
					}
					break;
				}
			}
			currentPosition += SIZE_OF_TYPE_INFO;
		}
	}
	
	public void listAllTypes() throws IOException{
		systemCatalog.seek(OFFSET_OF_NUM_OF_TYPES);
		int nofTypes = systemCatalog.readInt();
		String[] typeNames=new String[nofTypes];
		int count = 0;
		int filePointer = 8;
		byte[] stringBytes = new byte[8];
		while(filePointer < systemCatalog.length()){
			systemCatalog.seek(filePointer);
			boolean isValid = systemCatalog.readBoolean();
			if(isValid){
				systemCatalog.read(stringBytes);
				typeNames[count] = new String(stringBytes);
				count++;
			}
			filePointer += SIZE_OF_TYPE_INFO;
		}
		Arrays.sort(typeNames);
		for(int i=0;i<nofTypes;i++){
			output.println(typeNames[i]);
		}
	}
	
	public void createRecord(String typeName, ArrayList<Integer> fieldValues) throws FileNotFoundException, IOException{
		DataFile file = getDataFileOfType(typeName);
		file.addRecord(fieldValues);
	}
	
	public void deleteRecord(String typeName, int key) throws FileNotFoundException, IOException{
		DataFile file = getDataFileOfType(typeName);
		file.deleteRecord(key);
	}
	
	public void listRecords(String typeName) throws FileNotFoundException, IOException{
		DataFile file = getDataFileOfType(typeName);
		if(file == null){
			return ;
		}
		ArrayList<Record> records = file.listRecords();
		for(int i=0;i<records.size();i++){
			output.println(records.get(i).toString());
		}
	}
	
	public void updateRecord(String typeName, ArrayList<Integer> fieldValues) throws FileNotFoundException, IOException{
		DataFile file = getDataFileOfType(typeName);
		file.updateRecord(fieldValues);
	}
	
	public void searchRecord(String typeName, int key) throws FileNotFoundException, IOException{
		DataFile file = getDataFileOfType(typeName);
		if(file == null) return ;
		Record record = file.searchRecord(key);
		if(record !=null){
			output.println(record.toString());
		}
	}

	
	public int getIdToOpenFile() throws IOException{
		systemCatalog.seek(OFFSET_OF_FILE_IDS);
		int newFileId = systemCatalog.readInt()+1;
		systemCatalog.seek(OFFSET_OF_FILE_IDS);
		systemCatalog.writeInt(newFileId);
		return newFileId;
	}
	
	private DataFile getDataFileOfType(String typeName) throws FileNotFoundException, IOException{
		int currentPosition = 8;
		typeName = fillWithSpace(typeName);
		//Looks for all the valid types in the systemCatalog file.
		while(currentPosition < systemCatalog.length()){
			systemCatalog.seek(currentPosition);
			boolean isValid = systemCatalog.readBoolean();
			if(isValid){
				byte[] nameBytes = new byte[8];
				systemCatalog.read(nameBytes);
				String name = new String(nameBytes);
				if(name.equals(typeName)){
					systemCatalog.seek(currentPosition+9);
					int numOfFields = systemCatalog.readInt();
					systemCatalog.seek(currentPosition+OFFSET_OF_FILEPOINTER_IN_TYPEINFO);
					int fileId = systemCatalog.readInt();
					if(fileId<0){
						int newFileId = getIdToOpenFile();
						RandomAccessFile randomAccess = new RandomAccessFile(newFileId + ".txt","rw");
						DataFile file = new DataFile(randomAccess,false,numOfFields,this,newFileId);
						typeFileMap.put(typeName, file);
						systemCatalog.seek(currentPosition+OFFSET_OF_FILEPOINTER_IN_TYPEINFO);
						systemCatalog.writeInt(newFileId);
						return file;
					}
					else{
						if(typeFileMap.containsKey(typeName)){
							return typeFileMap.get(typeName);
						}
						else{
							DataFile file = new DataFile(new RandomAccessFile(fileId + ".txt","rw"),true,numOfFields,this,fileId);
							typeFileMap.put(typeName, file);
							return file;
						}
					}
				}
			}
			currentPosition += SIZE_OF_TYPE_INFO;
		}
		System.out.println("Error with DML operation. "+typeName+"doesn't exist yet." );
		return null;
	}
	
	/* Fills a string with white spaces till its length is 8.*/
	private String fillWithSpace(String a){
		StringBuffer build = new StringBuffer(a);
		for(int i=0;i<8-a.length();i++){
			build.append(" ");
		}
		return build.toString();
	}

}
