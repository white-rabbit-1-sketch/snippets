extends KinematicBody2D

class_name Creature

enum CreatureDirectionEnum {UP, DOWN, LEFT, RIGHT}

var creature_sprite: AnimatedSprite
var creature_collision_shape: CollisionShape2D
var ray_cast_front: RayCast2D
var ray_cast_up: RayCast2D
var ray_cast_down: RayCast2D
var creature_die_sound: AudioStreamPlayer
var creature_apply_damage_sound: AudioStreamPlayer
var creature_lifebar: Control

var state_machine: StateMachine = StateMachine.new(self)
var movement: BaseMovement

var creature_horizontal_direction: int
var creature_vertical_direction: int
var character_speed: float

var bounce_damage = 10

var health: int
var max_health: int

var stamina_point: int

func _ready():
	state_machine.add(CreatureStateHealth.new())
	state_machine.add(CreatureStateDie.new())
	state_machine.add(CreatureStateIdle.new())
	state_machine.add(CreatureStateBounceAttack.new())
	
func _physics_process(delta) -> void:
	movement.reset()
	creature_sprite.reset()
	
	state_machine.process()
	
	creature_sprite.priority_play()
		
	move_and_slide(movement.vector * character_speed, Vector2.UP)

func get_bounce_damage() -> int:
	return randi() % bounce_damage + 1
	
func apply_damage(damage: int) -> void:
	if not state_machine.is_active("die"):
		health -= damage
