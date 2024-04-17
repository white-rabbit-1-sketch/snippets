class_name ArrayHelper

static func array_unique(array: Array) -> Array:
	var result = []
	
	for item in array:
		if not item in result:
			result.append(item)
	
	return result

static func array_intersect(args: Array) -> Array:
	var result = []
	
	if len(args) == 0:
		return result
		
	var first_array = args[0]
		
	if len(args) == 1:
		return first_array
	
	for item in first_array:
		var item_present_everywhere = true
		for i in range(1, len(args) - 1):
			var second_array = args[i]
			if not item in second_array:
				item_present_everywhere = false
				break
				
		if not item_present_everywhere:
			continue
			
		result.append(item)
	
	return result
	
	
