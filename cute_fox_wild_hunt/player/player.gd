extends KinematicBody2D

onready var fox_character = $FoxKinematicBody
onready var leia_character = $LeiaKinematicBody
onready var transform_sprite = $TransformSprite
onready var transform_sound = $TransformSound

onready var CHARACTER_LIST = [fox_character, leia_character]

var movement: BaseMovement setget set_movement

var active_character
var is_alive = true

func _ready():
	# build character contexts
	leia_character.state_machine.process()
	disable_character(leia_character)
	activate_character(fox_character)

func _input(event: InputEvent):
	if event.is_action_pressed("transform"):
		transform()
		
func _physics_process(delta):
	global_position = active_character.global_position
	
func disable_character(character: Character):
	character.set_process(false)
	character.set_physics_process(false)
	character.set_process_input(false)
	character.character_collision_shape.disabled = true
	character.set_as_toplevel(false)
	character.hide()
	
func enable_character(character: Character):
	character.set_as_toplevel(true)
	character.global_position = global_position
	character.set_process(true)
	character.set_physics_process(true)
	character.set_process_input(true)
	character.character_collision_shape.disabled = false
	character.set_as_toplevel(true)
	character.show()

func activate_character(destination_character: Character):
	if active_character:
		transform_sprite.stop()
		transform_sprite.frame = 0
		transform_sprite.show()
		transform_sprite.play("transform")
		
		disable_character(active_character)
		copy_character_states(active_character, destination_character)
		
	
	enable_character(destination_character)
	
	active_character = destination_character
	
	yield(transform_sprite, "animation_finished")
	transform_sprite.hide()

func transform():
	if not active_character.state_machine.is_active("die"):
		for character in CHARACTER_LIST:
			if character == active_character:
				continue
			
			transform_sound.play()
			activate_character(character)
			
			break
		
func copy_character_states(source_character: Character, destination_character: Character) -> void:
	var destination_character_states: Array = destination_character.state_machine.states
	destination_character.state_machine.clear()

	for destination_character_state in destination_character_states:
		var source_character_state: BaseState = source_character.state_machine.get_state(
			destination_character_state.get_state_name()
		)
		if source_character_state:
			destination_character.state_machine.add(source_character_state)
			if source_character.state_machine.is_active(source_character_state.get_state_name()):
				destination_character.state_machine.active_states.append(source_character_state)
		else:
			destination_character.state_machine.add(destination_character_state)
			
			
	destination_character.health = int(round(
			floor(float(source_character.health) / (float(source_character.max_health) / 100))
			* (float(destination_character.max_health) / 100)
		))
		
func set_movement(world_movement: BaseMovement):
	fox_character.movement = world_movement
	leia_character.movement = world_movement
