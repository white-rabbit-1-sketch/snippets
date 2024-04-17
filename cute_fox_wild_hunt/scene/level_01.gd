extends Node

onready var player_kinematic_body = $PlayerKinematicBody

onready var oposum_body_1 = $OposumCreature
onready var oposum_body_2 = $OposumCreature2
onready var oposum_body_3 = $OposumCreature3

const GRAVITY_STRENGTH = 1.5

func _ready():
	var world_movement = BaseMovement.new(Vector2(0, GRAVITY_STRENGTH))
	
	player_kinematic_body.movement = world_movement.clone()
	oposum_body_1.movement = world_movement.clone()
	oposum_body_2.movement = world_movement.clone()
	oposum_body_3.movement = world_movement.clone()
