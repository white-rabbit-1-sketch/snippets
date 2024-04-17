class_name NodeHelper

static func get_nodes_by_paths(node: Node, nodes_patchs: Array) -> Array:
	var nodes = []
	
	for node_path in nodes_patchs:
		nodes.append(node.get_node(node_path) as Node)
		
	return nodes
