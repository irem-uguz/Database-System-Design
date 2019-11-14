
public class Record implements Comparable<Record>{
	int keyValue;
	int[] fieldValues;
	
	public Record(int key, int[] values){
		this.keyValue = key;
		this.fieldValues = values;
	}

	@Override
	public int compareTo(Record r) {
		// TODO Auto-generated method stub
		return this.keyValue - r.keyValue;
	}
	
	public String toString(){
		String record = "" + this.keyValue;
		for(int i = 0; i<this.fieldValues.length;i++){
			record +=" "+this.fieldValues[i];
		}
		return record;
	}

}
