import java.io.File;
import java.io.IOException;
import java.io.PrintStream;
import java.io.RandomAccessFile;
import java.util.ArrayList;
import java.util.Scanner;

public class storageManager {

	public static void main(String[] args) throws IOException {
		Scanner scan = new Scanner(new File(args[0]));
		PrintStream output = new PrintStream(new File(args[1]));
		SystemCatalog systemCatalog = new SystemCatalog(output);
		while(scan.hasNextLine()){
			String line = scan.nextLine();
			Scanner lineScanner = new Scanner(line);
			String command = lineScanner.next() + " " + lineScanner.next();
			if(command.equals("create type")){
				//Takes the inputs and creates a type.
				String typeName = lineScanner.next();
				int nofFields = lineScanner.nextInt();
				String[] fieldNames = new String[nofFields];
				for(int i=0;i<nofFields;i++){
					fieldNames[i]=lineScanner.next();
				}
				systemCatalog.createType(typeName, nofFields, fieldNames);
			}
			else if(command.equals("delete type")){
				//Takes the inputs and deletes the type.
				String typeName = lineScanner.next();
				systemCatalog.deleteType(typeName);
			}
			else if(command.equals("list type")){
				//Prints all types.
				systemCatalog.listAllTypes();
			}
			else if(command.equals("create record")){
				//Creates record.
				String typeName = lineScanner.next();
				ArrayList<Integer>  fieldValues = new ArrayList<Integer>();
				while(lineScanner.hasNextInt()){
					fieldValues.add(lineScanner.nextInt());
				}
				systemCatalog.createRecord(typeName, fieldValues);
			}
			else if(command.equals("delete record")){
				//Deletes a record.
				String typeName = lineScanner.next();
				int key = lineScanner.nextInt();
				systemCatalog.deleteRecord(typeName,key);		
			}
			else if(command.equals("list record")){
				//Lists all the records in that type
				String typeName = lineScanner.next();
				systemCatalog.listRecords(typeName);
			}
			else if(command.equals("update record")){
				//Updates the record with given values
				String typeName = lineScanner.next();
				ArrayList<Integer>  fieldValues = new ArrayList<Integer>();
				while(lineScanner.hasNextInt()){
					fieldValues.add(lineScanner.nextInt());
				}
				systemCatalog.updateRecord(typeName, fieldValues);
			}
			else if(command.equals("search record")){
				//Searches for the record
				String typeName = lineScanner.next();
				int key = lineScanner.nextInt();
				systemCatalog.searchRecord(typeName,key);		
			}
		}
	}

}
