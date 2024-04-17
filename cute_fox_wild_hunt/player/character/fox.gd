extends Character

class_name Fox

onready var character_jump_attack_collision_shape = $FoxAttackArea/FoxAttackCollisionShape
onready var character_jump_attack_area = $FoxAttackArea
onready var character_jump_attack_sound = $FoxJumpAttackSound

var jump_attack_damage = 10

func _ready():
	character_sprite = $FoxSprite
	character_jump_sound = $FoxJumpSound
	character_bounce_sound = $FoxBounceSound
	character_hurt_sound = $FoxHurtSound
	character_collision_shape = $FoxCollisionShape
	
	_on_ready()
	
	state_machine.add(CharacterStateJumpAttack.new())
	
	stamina_point = 4
