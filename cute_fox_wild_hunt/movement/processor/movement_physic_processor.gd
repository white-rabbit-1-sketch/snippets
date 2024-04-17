extends Node

class_name MovementPhysicProcessor

enum UpDirectionEnum {UP, DOWN, LEFT, RIGHT}

export(NodePath) onready var movement_node_path: NodePath
export(Vector2) onready var up_direction: Vector2 = Vector2.UP

onready var movement: ObjectMovement = get_node(movement_node_path)

func _physics_process(delta: float) -> void:
	movement.object.move_and_slide(movement.vector * movement.speed, up_direction)
