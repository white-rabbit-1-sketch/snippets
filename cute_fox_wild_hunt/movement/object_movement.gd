extends BaseMovement

class_name ObjectMovement

export(NodePath) onready var object_node_path: NodePath

onready var object: Node = get_node(object_node_path)
