extends KinematicBody2D

class_name Character

enum CharacterDirectionEnum {LEFT, RIGHT}

var character_sprite: AnimatedSprite
var character_collision_shape: CollisionShape2D
var character_jump_sound: AudioStreamPlayer
var character_bounce_sound: AudioStreamPlayer
var character_hurt_sound: AudioStreamPlayer

var state_machine: StateMachine = StateMachine.new(self)
var movement: BaseMovement

var character_direction: int = CharacterDirectionEnum.RIGHT
var character_speed: float = 120
var jump_strength: float = 2.5

var health: int
var max_health: int

var stamina_point: int

func _on_ready():
	state_machine.add(CharacterStateIdle.new())
	state_machine.add(CharacterStateMoveLeft.new())
	state_machine.add(CharacterStateMoveRight.new())
	state_machine.add(CharacterStateJump.new())
	state_machine.add(CharacterStateFall.new())
	state_machine.add(CharacterStateBounce.new())
	state_machine.add(CharacterStateHurt.new())
	state_machine.add(CharacterStateDirectionLeft.new())
	state_machine.add(CharacterStateDirectionRight.new())
	state_machine.add(CharacterStateHealth.new())
	state_machine.add(CharacterStateDie.new())
	
func _physics_process(delta) -> void:
	movement.reset()
	character_sprite.reset()
	
	if Input.is_action_pressed("move_left"): 
		state_machine.enter(state_machine.get_state(CharacterStateMoveLeft.get_state_name()))
	else: state_machine.leave(state_machine.get_state(CharacterStateMoveLeft.get_state_name()))
	
	if Input.is_action_pressed("move_right"): 
		state_machine.enter(state_machine.get_state(CharacterStateMoveRight.get_state_name()))
	else: state_machine.leave(state_machine.get_state(CharacterStateMoveRight.get_state_name()))
	
	if Input.is_action_pressed("jump"): 
		state_machine.enter(state_machine.get_state(CharacterStateJump.get_state_name()))
	
	state_machine.process()
	
	character_sprite.priority_play()

	move_and_slide(movement.vector * character_speed, Vector2.UP)
	
func bounce(bounce_direction: int) -> void:
	var context = {}
	context.bounce_direction = bounce_direction
	state_machine.enter(state_machine.get_state(CharacterStateBounce.get_state_name()), context)
	
func apply_damage(damage: float) -> void:
	var context = {}
	context.damage = damage
	state_machine.enter(state_machine.get_state(CharacterStateHurt.get_state_name()), context)
