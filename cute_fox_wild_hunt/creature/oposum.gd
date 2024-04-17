extends GroundCreature

class_name OposumCreature

func _ready():
	creature_sprite = $OposumSprite
	creature_collision_shape = $OposumCollisionShape
	ray_cast_front = $RayCastFront
	ray_cast_down = $RayCastDown
	ray_cast_up = $RayCastUp
	creature_die_sound = $OposumDieSound
	creature_apply_damage_sound = $OposumApplyDamageSound
	creature_lifebar = $CreatureLifeBar
	
	state_machine.add(CreatureStateBump.new())
	
	stamina_point = 10

func bump() -> void:
	state_machine.enter(state_machine.get_state(CreatureStateBump.get_state_name()))
	
func unbump() -> void:
	state_machine.leave(state_machine.get_state(CreatureStateBump.get_state_name()))
