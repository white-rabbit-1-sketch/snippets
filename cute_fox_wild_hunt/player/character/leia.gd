extends Character

class_name Leia

onready var character_melee_attack_collision_shape = $LeiaMeleeAttackArea/LeiaAttackCollisionShape
onready var character_melee_attack_area = $LeiaMeleeAttackArea
onready var character_melee_attack_sound = $LeiaMeleeAttackSound

var melee_attack_damage = 10

func _ready():
	character_sprite = $LeiaSprite
	character_jump_sound = $LeiaJumpSound
	character_bounce_sound = $LeiaBounceSound
	character_hurt_sound = $LeiaHurtSound
	character_collision_shape = $LeiaCollisionShape
	
	_on_ready()
	
	state_machine.add(CharacterStateMeleeAttack.new())
	
	stamina_point = 3
	
func _input(InputEvent: InputEvent) -> void:
	if InputEvent.is_action("melee_attack"): 
		state_machine.enter(state_machine.get_state(CharacterStateMeleeAttack.get_state_name()))
